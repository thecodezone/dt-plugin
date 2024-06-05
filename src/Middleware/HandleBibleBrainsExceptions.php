<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;
use function CodeZone\Bible\wp_die;

/**
 * Class HandleBibleBrainsExceptions
 * Middleware for handling BibleBrainsExceptions
 */
class HandleBibleBrainsExceptions implements Middleware {

	/**
	 * Handles the request.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param callable $next The next middleware callable.
	 *
	 * @return mixed The response from the next middleware.
	 *
	 * @throws BibleBrainsException If an exception occurs during processing.
	 */
	public function handle( Request $request, Response $response, callable $next ) {
		try {
			return $next( $request, $response );
		} catch ( BibleBrainsException $e ) {
			if ( $request->wantsJson() ) {
				$response->setContent( $e->getMessage() );
				$response->setStatusCode( 500 );

				return $response;
			} else {
				wp_die( esc_html( $e->getMessage() ), esc_attr( $e->getCode() ) );
			}
		}
	}
}
