#!/usr/bin/env bash

INCLUDE: ./activate-maintenance.sh

git fetch origin
git reset --hard origin/stable
INCLUDE: .getlocal.sh

bin/filemodes

INCLUDE: ./deactivate-maintenance.sh
