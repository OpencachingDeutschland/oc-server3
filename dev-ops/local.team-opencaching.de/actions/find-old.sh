#!/bin/bash
lines=`grep -R --exclude-dir=htdocs/vendor --exclude-dir=htdocs/app/Resources/translations --exclude-dir=htdocs/var/cache2/smarty/compiled --exclude-dir=htdocs/var/cache2/smarty/cache -n --color=always __SEARCH__ htdocs/*` && count=`echo " $lines" | wc -l` && echo "\033[0;31mFound $count occurrences:\033[0m" && echo "\033[0m$lines"
