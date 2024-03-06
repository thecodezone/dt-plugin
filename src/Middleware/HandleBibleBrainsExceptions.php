<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;
use function CodeZone\Bible\wp_die;

class HandleBibleBrainsExceptions implements Middleware {

	public function handle( Request $request, Response $response, callable $next ) {
		try {
			return $next( $request, $response );
		} catch ( BibleBrainsException $e ) {
			if ( $request->wantsJson() ) {
				$response->setContent( $e->getMessage() );
				$response->setStatusCode( 500 );

				return $response;
			} else {
				wp_die( $e->getMessage(), $e->getCode() );
			}
		}
	}
}
