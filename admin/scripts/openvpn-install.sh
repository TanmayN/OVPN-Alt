#!/bin/bash

echo "Script by Nyr on GitHub with modifications by me"
echo "https://github.com/Nyr/openvpn-install"
echo "OpenVPN road warrior installer for Debian-based distros."

if [ $USER != 'root' ]; then
        echo "Sorry, you need to run this as root"
        exit
fi


if [ ! -e /dev/net/tun ]; then
    echo "TUN/TAP is not available"
    exit
fi


# Try to get our IP from the system and fallback to the Internet.
# I do this to make the script compatible with NATed servers (lowendspirit.com)
# and to avoid getting an IPv6.
IP=$(ifconfig | grep 'inet addr:' | grep -v inet6 | grep -vE '127\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' | cut -d: -f2 | awk '{ print $1}' | head -1)
if [ "$IP" = "" ]; then
        IP=$(wget -qO- ipv4.icanhazip.com)
fi

if [ -e /etc/openvpn/server.conf ]; then
	echo "Looks like OpenVPN is already installed."
	
else
	PORT="1194"
	ALTPORT="n"
	CLIENT=`hostname`
	apt-get update
    apt-get install openvpn iptables sshpass openssl -y
    cp -R /usr/share/doc/openvpn/examples/easy-rsa/ /etc/openvpn
    # easy-rsa isn't available by default for Debian Jessie and newer
    if [ ! -d /etc/openvpn/easy-rsa/2.0/ ]; then
            wget --no-check-certificate -O ~/easy-rsa.tar.gz https://github.com/OpenVPN/easy-rsa/archive/2.2.2.tar.gz
            tar xzf ~/easy-rsa.tar.gz -C ~/
            mkdir -p /etc/openvpn/easy-rsa/2.0/
            cp ~/easy-rsa-2.2.2/easy-rsa/2.0/* /etc/openvpn/easy-rsa/2.0/
            rm -rf ~/easy-rsa-2.2.2
            rm -rf ~/easy-rsa.tar.gz
    fi
    cd /etc/openvpn/easy-rsa/2.0/
    # Let's fix one thing first...
    cp -u -p openssl-1.0.0.cnf openssl.cnf
    # Fuck you NSA - 1024 bits was the default for Debian Wheezy and older
    sed -i 's|export KEY_SIZE=1024|export KEY_SIZE=2048|' /etc/openvpn/easy-rsa/2.0/vars
    # Create the PKI
    . /etc/openvpn/easy-rsa/2.0/vars
    . /etc/openvpn/easy-rsa/2.0/clean-all
    # The following lines are from build-ca. I don't use that script directly
    # because it's interactive and we don't want that. Yes, this could break
    # the installation script if build-ca changes in the future.
    export EASY_RSA="${EASY_RSA:-.}"
    "$EASY_RSA/pkitool" --initca $*
    # Same as the last time, we are going to run build-key-server
    export EASY_RSA="${EASY_RSA:-.}"
    "$EASY_RSA/pkitool" --server server
    # Now the client keys. We need to set KEY_CN or the stupid pkitool will cry
    export KEY_CN="$CLIENT"
    export EASY_RSA="${EASY_RSA:-.}"
    "$EASY_RSA/pkitool" $CLIENT
    # DH params
    . /etc/openvpn/easy-rsa/2.0/build-dh
    # Let's configure the server
    cd /usr/share/doc/openvpn/examples/sample-config-files
    gunzip -d server.conf.gz
    cp server.conf /etc/openvpn/
    cd /etc/openvpn/easy-rsa/2.0/keys
    cp ca.crt ca.key dh2048.pem server.crt server.key /etc/openvpn
    cd /etc/openvpn/
    # Set the server configuration
    sed -i 's|dh dh1024.pem|dh dh2048.pem|' server.conf
    sed -i 's|;push "redirect-gateway def1 bypass-dhcp"|push "redirect-gateway def1 bypass-dhcp"|' server.conf
    sed -i 's|;push "dhcp-option DNS 208.67.222.222"|push "dhcp-option DNS 129.250.35.250"|' server.conf
    sed -i 's|;push "dhcp-option DNS 208.67.220.220"|push "dhcp-option DNS 74.82.42.42"|' server.conf
    sed -i "s|port 1194|port $PORT|" server.conf
	echo "plugin /usr/lib/openvpn/openvpn-auth-pam.so authentication" >> server.conf
    # Listen at port 53 too if user wants that
    if [ $ALTPORT = 'y' ]; then
            iptables -t nat -A PREROUTING -p udp -d $IP --dport 53 -j REDIRECT --to-port 1194
            sed -i "/# By default this script does nothing./a\iptables -t nat -A PREROUTING -p udp -d $IP --dport 53 -j REDIRECT --to-port 1194" /etc/rc.local
    fi
    # Enable net.ipv4.ip_forward for the system
    sed -i 's|#net.ipv4.ip_forward=1|net.ipv4.ip_forward=1|' /etc/sysctl.conf
    # Avoid an unneeded reboot
    echo 1 > /proc/sys/net/ipv4/ip_forward
    # Set iptables
    iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -j SNAT --to $IP
    sed -i "/# By default this script does nothing./a\iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -j SNAT --to $IP" /etc/rc.local
    # And finally, restart OpenVPN
    /etc/init.d/openvpn restart
    # Let's generate the client config
    mkdir ~/ovpn-$CLIENT
    # Try to detect a NATed connection and ask about it to potential LowEndSpirit
    # users
	EXTERNALIP=$(wget -qO- ipv4.icanhazip.com)
	if [ "$IP" != "$EXTERNALIP" ]; then
		USEREXTERNALIP=$EXTERNALIP
	fi
fi
sed -i "s|remote my-server-1 1194|remote $IP $PORT|" /usr/share/doc/openvpn/examples/sample-config-files/client.conf
echo "auth-user-pass" >> /usr/share/doc/openvpn/examples/sample-config-files/client.conf
cp /usr/share/doc/openvpn/examples/sample-config-files/client.conf ~/ovpn-$CLIENT/$CLIENT.conf
cp /etc/openvpn/easy-rsa/2.0/keys/ca.crt ~/ovpn-$CLIENT
cp /etc/openvpn/easy-rsa/2.0/keys/$CLIENT.crt ~/ovpn-$CLIENT
cp /etc/openvpn/easy-rsa/2.0/keys/$CLIENT.key ~/ovpn-$CLIENT
cd ~/ovpn-$CLIENT
sed -i "s|cert client.crt|cert $CLIENT.crt|" $CLIENT.conf
sed -i "s|key client.key|key $CLIENT.key|" $CLIENT.conf
tar -czf ../ovpn-$CLIENT.tar.gz $CLIENT.conf ca.crt $CLIENT.crt $CLIENT.key
cd ~/
rm -rf ovpn-$CLIENT
mv ovpn-$CLIENT.tar.gz /var/www/html/configs