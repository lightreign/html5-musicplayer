#!/usr/bin/env bash
#
# Run Local PHP Server
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

cd public/
$php -S localhost:$port
