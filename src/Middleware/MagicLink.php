<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;
use DT_Magic_Url_Base;
use function CodeZone\Bible\container;

/**
 * Class MagicLink
 *
 * Represents a middleware that checks the magic link before calling the next middleware.
 */
class MagicLink implements Middleware {
	protected DT_Magic_Url_Base $magic_link;

	/**
	 * Class constructor.
	 *
	 * @param string|object $magic_link The magic link or name of the magic link service.
	 */
	public function __construct( $magic_link ) {
		if ( is_string( $magic_link ) ) {
			$magic_link = container()->make( $magic_link );
		}
		$this->magic_link = $magic_link;
	}

	/**
	 * Handle the request by checking the magic link and calling the next middleware.
	 *
	 * @param Request $request The HTTP request object.
	 * @param Response $response The HTTP response object.
	 * @param callable $next The next middleware closure.
	 *
	 * @return Response The modified HTTP response object.
	 */
	public function handle( Request $request, Response $response, callable $next ) {
		if ( ! $this->magic_link || ! $this->magic_link->check_parts_match() ) {
			$response->setStatusCode( 404 );
		}

		return $next( $request, $response );
	}
}