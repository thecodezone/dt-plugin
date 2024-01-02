<?php

namespace DT\Plugin\Providers;

use DT\Plugin\CodeZone\Router\Middleware\DispatchController;
use DT\Plugin\CodeZone\Router\Middleware\HandleErrors;
use DT\Plugin\CodeZone\Router\Middleware\HandleRedirects;
use DT\Plugin\CodeZone\Router\Middleware\Middleware;
use DT\Plugin\CodeZone\Router\Middleware\Render;
use DT\Plugin\CodeZone\Router\Middleware\Route;
use DT\Plugin\CodeZone\Router\Middleware\Stack;
use DT\Plugin\CodeZone\Router\Middleware\UserHasCap;
use DT\Plugin\Middleware\LoggedIn;
use DT\Plugin\Middleware\LoggedOut;
use DT\Plugin\Middleware\MagicLink;

/**
 * Request middleware to be used in the request lifecycle.
 *
 * Class MiddlewareServiceProvider
 * @package DT\Plugin\Providers
 */
class MiddlewareServiceProvider extends ServiceProvider {
	protected $middleware = [
		Route::class,
		DispatchController::class,
		HandleErrors::class,
		HandleRedirects::class,
		Render::class,
	];

	protected $route_middleware = [
		'auth'  => LoggedIn::class,
		'can'   => UserHasCap::class,
		'guest' => LoggedOut::class,
		'magic' => MagicLink::class
	];

	/**
	 * Registers the middleware for the plugin.
	 *
	 * This method adds a filter to register middleware for the plugin.
	 * The middleware is added to the stack in the order it is defined above.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'dt/plugin/middleware', function ( Stack $stack ) {
			$stack->push( ...$this->middleware );

			return $stack;
		} );

		add_filter( 'codezone/router/middleware', function ( array $middleware ) {
			return array_merge( $middleware, $this->route_middleware );
		} );

		add_filter( 'codezone/router/middleware/factory', function ( Middleware|null $middleware, $attributes ) {
			$classname = $attributes['className'] ?? null;
			$name      = $attributes['name'] ?? null;
			$signature = $attributes['signature'] ?? null;

			//Add constructor arguments to the magic link middleware
			if ( $name === 'magic' ) {

				//Resolve the magic link by name from the signature
				$magic_link_name       = $signature;
				$magic_link_class_name = $this->container->make( 'DT\Plugin\MagicLinks' )->get( $magic_link_name );
				$magic_link            = $this->container->make( $magic_link_class_name );

				//The signature is the part of the route name after the ":". We need to break it into an array.
				return $this->container->makeWith( $classname, [
					'magic_link' => $magic_link
				] );
			}

			return $middleware;
		}, 10, 2 );
	}

	/**
	 * Do anything we need to do after the theme loads.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}