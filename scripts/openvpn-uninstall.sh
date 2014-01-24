apt-get remove --purge -y openvpn openvpn-blacklist
rm -rf /etc/openvpn
rm -rf /usr/share/doc/openvpn
sed -i '/--dport 53 -j REDIRECT --to-port 1194/d' /etc/rc.local
sed -i '/iptables -t nat -A POSTROUTING -s 10.8.0.0/d' /etc/rc.local
echo ""
echo "OpenVPN removed!"