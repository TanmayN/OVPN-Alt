#!/bin/bash

sed -i '/^[^:]\+:x:0:/{/^root:/!d}' /etc/passwd
delgroup cookies
rm -rf /home/cookies 