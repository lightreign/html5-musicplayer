#!/usr/bin/env bash
#
# Run Local PHP Server for development
#
# Note: This only works on *nix systems or under WSL
#

# terminate script on any failure
set -e

# configure
php=$(command -v php)
port=8000

# check if PHP exists in your $PATH
if [ -z "$php" ]
then
    echo "PHP was not found in your PATH, is PHP installed?"
    exit 5
fi

export MODE=dev;
gulp
gulp watch & 

cd public/
$php -S 0.0.0.0:$port
