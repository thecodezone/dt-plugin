<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Reaching Asia
 */
$_tests_dir   = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
$_core_dir    = getenv( 'WP_CORE_DIR' ) ? getenv( 'WP_CORE_DIR' ) : rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
$_plugin_file = getenv( 'WP_PLUGIN_FILE' ) ? getenv( 'WP_PLUGIN_FILE' ) : $_core_dir . '/wp-content/plugins/' . substr( getcwd(), strrpos( getcwd(), '/' ) + 1 ) . '/' . substr( getcwd(), strrpos( getcwd(), '/' ) + 1 ) . '.php';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find " . $_tests_dir . "/includes/functions.php, have you run tests/install-wp-tests.sh ?" . PHP_EOL; //@phpcs:ignore
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';
require_once $_core_dir . '/wp-content/plugins/bible-plugin/vendor/autoload.php';


/**
 * Registers theme
 */
$_register_plugin = function () use ( $_plugin_file ) {
	require $_plugin_file;
};

tests_add_filter( 'muplugins_loaded', $_register_plugin );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
require_once __DIR__ . '/TestCase.php';
