#!/usr/bin/env bash

INCLUDE: ./activate-maintenance.sh

git fetch origin
git reset --hard origin/next
INCLUDE: .getlocal.sh

bin/filemodes

INCLUDE: ./deactivate-maintenance.sh
