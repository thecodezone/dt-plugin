#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")/../"

if [ "$(php -r 'echo version_compare( phpversion(), "7.0", ">=" ) ? 1 : 0;')" != 1 ] ; then
    php -l ../bible-plugin.php
    exit
fi

found_error=0

# specify directories to be checked
dirs_to_check=("src" "resources/views")

for dir in "${dirs_to_check[@]}" ; do
    while read -d '' filename ; do
        # php -l checks the file for syntax errors
        php -l "$filename" || found_error=1
    done < <(find $dir -name "*.php" -print0)
done

exit $found_error
