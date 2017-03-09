#!/usr/bin/env bash

git fetch origin

I: sh bin/filemodes

git merge origin/stable

I: sh bin/filemodes
