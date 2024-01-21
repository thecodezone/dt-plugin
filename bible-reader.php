<?php
/**
 * Plugin Name: Bible Reader
 * Plugin URI: https://github.com/TheCodeZone/bible-reader
 * Description: A bible plugin for WordPress.
 * Text Domain: bible-reader
 * Domain Path: /languages
 * Version:  0.1
 * Authors: Reaching Asia, CodeZone
 * Author URI: https://github.com/TheCodeZone
 * GitHub Plugin URI: https://github.com/TheCodeZone/bible-reader
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 */

use CodeZone\Bible\Illuminate\Container\Container;
use CodeZone\Bible\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . '/includes/class-tgm-plugin-activation.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

$container = new Container();
$container->singleton( Container::class, function ( $container ) {
	return $container;
} );
$container->singleton( Plugin::class, function ( $container ) {
	return new Plugin( $container );
} );
$plugin_instance = $container->make( Plugin::class );
$plugin_instance->init();
