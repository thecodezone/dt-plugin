<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

/**
 * Class Nonce
 *
 * @package AppBundle\Middleware
 */
class Nonce implements Middleware {
	/**
	 * Name of the nonce.
	 *
	 * @var string
	 */
	protected $nonce_name;

	/**
	 * Constructor for initializing the nonce name.
	 *
	 * @param string $nonce_name The nonce name to be set.
	 */
	public function __construct( $nonce_name ) {
		$this->nonce_name = $nonce_name;
	}

	/**
	 * Handles the request by verifying the nonce.
	 *
	 * @param Request $request The Request object.
	 * @param Response $response The Response object.
	 * @param mixed $next The callback function to be called next.
	 *
	 * @return mixed Returns the output of the callback function.
	 */
	public function handle( Request $request, Response $response, $next ) {
		$nonce = $request->header( 'X-WP-Nonce' )
		         ?? $request->header( 'x-wp-nonce' )
		            ?? $request->input( '_wpnonce' );

		if ( empty( $nonce ) ) {
			$response->setContent( __( 'Could not verify request.', 'bible-plugin' ) );

			return $response->setStatusCode( 403 );
		}

		if ( ! wp_verify_nonce( $nonce, $this->nonce_name ) ) {
			return $response->setStatusCode( 403 );
		}

		return $next( $request, $response );
	}
}
