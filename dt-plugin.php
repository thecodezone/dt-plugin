<?php
/**
 * Plugin Name: DT Plugin
 * Plugin URI: https://github.com/thecodezone/dt_plugin
 * Description: A modern disciple.tools plugin starter template.
 * Text Domain: dt-plugin
 * Domain Path: /languages
 * Version:  0.1
 * Author URI: https://github.com/TheCodeZone
 * GitHub Plugin URI: https://github.com/thecodezone/dt_plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/thecodezone
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

use DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Plugin\Plugin;
use DT\Plugin\Providers\ConfigServiceProvider;
use DT\Plugin\Providers\PluginServiceProvider;
use DT\Plugin\Providers\RewritesServiceProvider;
use DT\Plugin\CodeZone\WPSupport\Container\ContainerFactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Load dependencies

require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Create the IOC container
$container = ContainerFactory::singleton();

require_once plugin_dir_path( __FILE__ ) . 'src/helpers.php';

// Add any services providers required to init the plugin
$boot_providers = [
	ConfigServiceProvider::class,
	RewritesServiceProvider::class,
	PluginServiceProvider::class
];

foreach ( $boot_providers as $provider ) {
	$container->addServiceProvider( $container->get( $provider ) );
}

// Init the plugin
$dt_plugin = $container->get( Plugin::class );
$dt_plugin->init();

// Add the rest of the service providers
$config = $container->get( ConfigInterface::class );
foreach ( $config->get( 'services.providers' ) as $provider ) {
	$container->addServiceProvider( $container->get( $provider ) );
}
