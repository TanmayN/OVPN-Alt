#!/bin/bash

sudo su

useradd -s /sbin/nologin $1
echo "${1}:${2}" | chpasswd