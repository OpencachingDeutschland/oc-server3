#!/usr/bin/env bash

systemctl stop httpd.service

git fetch origin
git reset --hard origin/next
INCLUDE: .getlocal.sh

# bin/filemodes

systemctl start httpd.service
