#!/bin/bash

sudo su

CLIENT=$1
HOME=$(sudo -Hiu www-data env | grep HOME | cut -f2 -d"=")/OVPN-Alt/configs

 cd /etc/openvpn/easy-rsa/2.0/
sudo /etc/openvpn/easy-rsa/2.0/vars
sudo /etc/openvpn/easy-rsa/2.0/revoke-full $CLIENT
# If it's the first time revoking a cert, we need to add the crl-verify line
if grep -q "sudo crl-verify" "/etc/openvpn/server.conf"; then
	echo ""
	echo "Certificate for client $CLIENT revoked"
else
	sudo echo "crl-verify /etc/openvpn/easy-rsa/2.0/keys/crl.pem" >> "/etc/openvpn/server.conf"
	sudo /etc/init.d/openvpn restart
	echo ""
	echo "Certificate for client $CLIENT revoked"
fi

cd $HOME
sudo rm ovpn-$CLIENT.tar.gz