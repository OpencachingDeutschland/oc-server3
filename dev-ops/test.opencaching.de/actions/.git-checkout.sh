#!/usr/bin/env bash

git fetch origin

I: sh bin/filemodes

git merge origin/next

I: sh bin/filemodes
