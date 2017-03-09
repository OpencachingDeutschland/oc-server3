#!/usr/bin/env bash

if [ "`git branch|grep stable`" != "* stable" ]; then
  echo "error: you are not in stable branch!"
  exit 1;
fi

if [ "x`git status|grep -i 'working directory clean'`"  == "x" ]; then
    echo "error: working directory is not clean:"
    git status
    exit 1;
fi
