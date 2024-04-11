<?php

namespace CodeZone\Bible;

use CodeZone\Bible\CodeZone\Router\Middleware\Stack;
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
		add_action( 'wp_loaded', [ $this, 'wp_loaded' ], 20 );
		add_action( 'init', [ $this, 'rewrite_rules' ] );
		add_action( 'query_vars', [ $this, 'query_vars' ] );
		add_action( 'template_redirect', [ $this, 'template_redirect' ] );
	}

	/**
	 * Runs after wp_loaded
	 * @return void
	 */
	public function wp_loaded(): void {
		$this->provider->boot();
	}

	/**
	 * Rewrite rules method.
	 *
	 * This method is responsible for adding any custom rewrite rules to the plugin.
	 * We'll use this method to add a custom rewrite rule for the all routes prefixed
	 * with the plugin's home route. Subsequent routes will be handled by the plugin's
	 * router.
	 *
	 * @return void
	 */
	public function rewrite_rules(): void {
		add_rewrite_rule( '^' . self::$home_route . '/?', 'index.php?bible-plugin=true', 'top' );
	}

	/**
	 * Add query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function query_vars( array $vars ): array {
		$vars[] = 'bible-plugin';

		return $vars;
	}

	/**
	 * Perform template redirect based on query var 'dt_autolink'.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function template_redirect(): void {
		if ( ! get_query_var( 'bible-plugin' ) ) {
			return;
		}

		$response = apply_filters( namespace_string( 'middleware' ), $this->container->make( Stack::class ) )
			->run();

		if ( ! $response ) {
			wp_die( esc_attr( __( "The page could not be found.", 'dt-plugin' ) ), 404 );
		}

		if ( ! $response->isSuccessful() ) {
			wp_die( esc_attr( $response->statusText() ), esc_attr( $response->getStatusCode() ) );
		}

		$path = get_theme_file_path( 'template-blank.php' );
		include $path;

		die();
	}
}
