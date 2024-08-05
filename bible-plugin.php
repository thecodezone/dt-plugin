<?php
/**
 * Plugin Name: The Bible Plugin
 * Plugin URI: https://github.com/Reaching-Asia-Inc/The-Bible-Plugin
 * Description: A bible plugin for WordPress.
 * Text Domain: bible-plugin
 * Domain Path: /languages
 * Version:  1.0.0-beta8.4
 * Author: Reaching Asia
 * Author URI: https://reachingasia.com
 * GitHub Plugin URI: https://github.com/Reaching-Asia-Inc/The-Bible-Plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 */

use CodeZone\Bible\Dotenv\Dotenv;
use CodeZone\Bible\Illuminate\Container\Container;
use CodeZone\Bible\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-tgm-plugin-activation.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.env' ) ) {
    $dotenv = Dotenv::createImmutable( __DIR__ );
    $dotenv->load();
}

$container = new Container();
$container->singleton(Container::class, function ( $container ) {
    return $container;
});
$container->singleton(Plugin::class, function ( $container ) {
    return new Plugin( $container );
});
$plugin_instance = $container->make( Plugin::class );
$plugin_instance->init();
