<?php

namespace CodeZone\Bible;

use CodeZone\Bible\Illuminate\Container\Container;
use CodeZone\Bible\Providers\PluginServiceProvider;

/**
 * This is the entry-object for the plugin.
 * Handle any setup and bootstrapping here.
 */
class Plugin {
	/**
	 * The route for the plugin's home page
	 * @var string
	 */
	public static $home_route = 'bible';

	/**
	 * The instance of the plugin
	 * @var Plugin
	 */
	public static Plugin $instance;

	/**
	 * The container
	 * @see https://laravel.com/docs/10.x/container
	 * @var Container
	 */
	public Container $container;

	/**
	 * The service provider
	 * @see https://laravel.com/docs/10.x/providers
	 * @var PluginServiceProvider
	 */
	public PluginServiceProvider $provider;

	/**
	 * Plugin constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container  = $container;
		self::$home_route = apply_filters( namespace_string( 'route' ), self::$home_route );
		$this->provider   = $container->make( PluginServiceProvider::class );
	}

	/**
	 * Get the instance of the plugin
	 * @return void
	 */
	public function init() {
		static::$instance = $this;
		$this->provider->register();
		add_action( 'init', function () {
			$this->provider->boot();
		}, 20 );
	}
}
