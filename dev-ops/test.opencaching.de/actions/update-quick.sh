#!/usr/bin/env bash

# This script may be used for a quick release with HTTP UP, if only
# few, rarely  used files are updated and no database updates are needed.
# The updated files will be inaccessible until the 'filemodes' script
# has fixed access rights.

sh ./dev-ops/test.opencaching.de/actions/.check-git-status.sh

git pull
I: sh bin/filemodes
