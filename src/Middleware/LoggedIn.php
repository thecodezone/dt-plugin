<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

/**
 * Class representing a middleware that checks if a user is logged in.
 */
class LoggedIn implements Middleware {
	/**
	 * Handles the request by checking if the user is logged in, redirecting to the login page if they are not, and calling the next middleware.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param callable $next The next middleware.
	 *
	 * @return Response The response after handling the request.
	 */
	public function handle( Request $request, Response $response, $next ) {
		if ( ! is_user_logged_in() ) {
			$response = new RedirectResponse( wp_login_url( $request->getUri() ), 302 );
		}

		return $next( $request, $response );
	}
}