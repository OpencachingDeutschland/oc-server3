#!/usr/bin/env bash

git fetch origin

I: sh bin/filemodes

systemctl stop httpd.service

git merge origin/next

I: sh bin/filemodes
