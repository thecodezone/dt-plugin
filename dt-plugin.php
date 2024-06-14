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

use DT\Plugin\League\Config\Configuration;
use DT\Plugin\League\Container\Container;
use DT\Plugin\League\Container\ReflectionContainer;
use DT\Plugin\Plugin;
use DT\Plugin\Providers\ConfigServiceProvider;
use DT\Plugin\Providers\PluginServiceProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Load dependencies

require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

// Create the IOC container
$container = new Container();
$container->delegate( new ReflectionContainer() );

// Add any services providers required to init the plugin
$container->addServiceProvider( new ConfigServiceProvider() );
$container->addServiceProvider( new PluginServiceProvider() );

// Init the plugin
$plugin = $container->get( Plugin::class );
$plugin->init();

// Add the rest of the service providers
$config = $container->get(Configuration::class);
foreach ($config->get('services.providers') as $provider ) {
    $container->addServiceProvider(  $container->get( $provider ) );
}
