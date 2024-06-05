#!/bin/bash

# Test installing this plugin using the Wordpress CLI, with a command like this
# one:
#
# wp plugin install --activate (github link)/archive/master.zip
#
# If this fails, we know we have an issue that we need to fix to make the plugin
# installable again


set -x
set -e

tmpdir=$(mktemp -d)

cd "$tmpdir"

curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
php wp-cli.phar --info
chmod +x wp-cli.phar

# Set up basic Wordpress installation:
./wp-cli.phar core download  --allow-root
./wp-cli.phar config create --force --dbname=testdb --dbuser=user --dbhost=127.0.0.0 --dbpass=password --allow-root
./wp-cli.phar core install --url=localhost --title=test --admin_user=admin --admin_email=example@example.com --allow-root

# Install plugin
./wp-cli.phar plugin install --allow-root --activate $GITHUB_WORKSPACE/bible-plugin.zip
