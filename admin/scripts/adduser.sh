#!/bin/bash

sudo su

CLIENT=$1
HOME=$(sudo -Hiu www-data env | grep HOME | cut -f2 -d"=")/OVPN-Alt/configs

cd /etc/openvpn/easy-rsa/2.0/
source ./vars
# build-key for the client
export KEY_CN="$CLIENT"
export EASY_RSA="${EASY_RSA:-.}"
"$EASY_RSA/pkitool" $CLIENT
# Let's generate the client config
mkdir ~/ovpn-$CLIENT
cp /usr/share/doc/openvpn/examples/sample-config-files/client.conf ~/ovpn-$CLIENT/$CLIENT.ovpn
cp /etc/openvpn/easy-rsa/2.0/keys/ca.crt ~/ovpn-$CLIENT
cp /etc/openvpn/easy-rsa/2.0/keys/$CLIENT.crt ~/ovpn-$CLIENT
cp /etc/openvpn/easy-rsa/2.0/keys/$CLIENT.key ~/ovpn-$CLIENT
cd ~/ovpn-$CLIENT
sed -i "s|cert client.crt|cert $CLIENT.crt|" $CLIENT.ovpn
sed -i "s|key client.key|key $CLIENT.key|" $CLIENT.ovpn
tar -czf ../ovpn-$CLIENT.tar.gz $CLIENT.ovpn ca.crt $CLIENT.crt $CLIENT.key
cd ~/
rm -rf ovpn-$CLIENT
echo ""
echo "Client $CLIENT added, certs available at ~/ovpn-$CLIENT.tar.gz"