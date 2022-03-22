#!/bin/bash
grep -R --exclude-dir=htdocs/app/Resources/translations --exclude-dir=htdocs/var/cache2/smarty/compiled --exclude-dir=htdocs/var/cache2/smarty/cache -n --color=auto $1 htdocs/*
