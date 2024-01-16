<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\CodeZone\Router\FastRoute\Routes;
use CodeZone\Bible\CodeZone\Router\Middleware\Stack;
use CodeZone\Bible\CodeZone\Router\Router;
use CodeZone\Bible\FastRoute\RouteCollector;
use CodeZone\Bible\Illuminate\Http\Response;
use function CodeZone\Bible\routes_path;

class RouterServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
		Router::register( [
			'container' => $this->container,
		] );

		add_filter( "codezone/router/routes", [ $this, 'include_route_file' ], 1 );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function boot(): void {
		if ( is_admin() ) {
			return;
		}

		apply_filters( 'codezone/bible/middleware', $this->container->make( Stack::class ) )
			->run();
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
