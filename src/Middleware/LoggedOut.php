<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Plugin;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

class LoggedOut implements Middleware {

	public function handle( Request $request, Response $response, $next ) {
		if ( is_user_logged_in() ) {
			$response = new RedirectResponse( '/' . Plugin::HOME_ROUTE, 302 );

		}

		return $next( $request, $response );
	}
}
