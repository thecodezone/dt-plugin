<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Plugin;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

/**
 * Class LoggedOut
 *
 * This class is a middleware that checks if the user is logged out and performs a redirect if they are logged in.
 *
 * @package App\Middleware
 */
class LoggedOut implements Middleware {

	/**
	 * Handles the request and response.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param callable $next The next middleware or controller action.
	 *
	 * @return mixed The result of the next middleware or controller action.
	 */
	public function handle( Request $request, Response $response, $next ) {
		if ( is_user_logged_in() ) {
			$response = new RedirectResponse( '/' . Plugin::HOME_ROUTE, 302 );

		}

		return $next( $request, $response );
	}
}
