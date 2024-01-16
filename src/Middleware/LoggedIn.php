<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

class LoggedIn implements Middleware {
	public function handle( Request $request, Response $response, $next ) {
		if ( ! is_user_logged_in() ) {
			$response = new RedirectResponse( wp_login_url( $request->getUri() ), 302 );
		}

		return $next( $request, $response );
	}
}