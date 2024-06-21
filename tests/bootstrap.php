<?php

// phpcs:disable
$_tests_dir   = $_ENV['WP_TESTS_DIR'] ?? false ?: rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
$_core_dir    = $_ENV['WP_CORE_DIR'] ?? false ?: rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
$_plugin_file = $_ENV['WP_PLUGIN_FILE'] ?? false ?: dirname(__DIR__) . '/bible-plugin.php';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find " . $_tests_dir . "/includes/functions.php, have you run tests/install-wp-tests.sh ?" . PHP_EOL; //@phpcs:ignore
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Registers theme
 */
$_register_plugin = function () use ( $_plugin_file ) {
    if (!empty($_ENV['TBP_BIBLE_BRAINS_KEYS'])) {
        define( 'TBP_BIBLE_BRAINS_KEYS', $_ENV['TBP_BIBLE_BRAINS_KEYS'] );
    }

    require $_plugin_file;
};


tests_add_filter( 'muplugins_loaded', $_register_plugin );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
