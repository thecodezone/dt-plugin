<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\CodeZone\Router;
use CodeZone\Bible\CodeZone\Router\FastRoute\Routes;
use CodeZone\Bible\CodeZone\Router\Middleware\Stack;
use CodeZone\Bible\FastRoute\RouteCollector;
use function CodeZone\Bible\namespace_string;
use function CodeZone\Bible\routes_path;

class RouterServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
		Router::register( [
			'container'   => $this->container,
			'route_param' => 'bible-plugin-route',
		] );

		add_filter( Router\namespace_string( "routes" ), [ $this, 'include_route_file' ], 1 );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function boot(): void {
	}

	/**
	 * Register the routes for the application.
	 *
	 * @param Routes $r
	 *
	 * @return Routes
	 */
	public function include_route_file( Routes $r ): RouteCollector {

		include routes_path( 'web.php' );

		return $r;
	}
}
