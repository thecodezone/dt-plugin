<?php

namespace DT\Plugin\Providers;

use DT\Plugin\CodeZone\Router;
use DT\Plugin\CodeZone\Router\FastRoute\Routes;
use DT\Plugin\CodeZone\Router\Middleware\Stack;
use DT\Plugin\FastRoute\RouteCollector;
use DT\Plugin\Illuminate\Http\Response;
use function DT\Plugin\namespace_string;
use function DT\Plugin\routes_path;

class RouterServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		Router::register( [
			'container' => $this->container,
		] );

		add_filter( Router\namespace_string( "routes" ), [ $this, 'include_route_file' ], 1 );
		add_action( Router\namespace_string( 'render' ), [ $this, 'render_response' ], 10, 2 );
	}

	/**
	 * Do any setup needed after the theme is ready.
	 * DT is registered.
	 *
	 * @return void
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
