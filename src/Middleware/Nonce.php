<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Psr\Http\Server\MiddlewareInterface;
use DT\Plugin\Psr\Http\Server\RequestHandlerInterface;
use function DT\Plugin\config;
use function DT\Plugin\response;

class Nonce implements MiddlewareInterface {
	protected $nonce_name;

	public function __construct( $nonce_name = '' ) {
		$this->nonce_name = $nonce_name ?? config('plugin.nonce_name');
	}

    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        $nonce = $request->getHeader( 'X-WP-Nonce' ) ?? get_query_var( '_wpnonce' );

        if ( empty( $nonce ) ) {
            return response( 'Nonce is required.', 403 );
        }

        if ( ! wp_verify_nonce( $nonce, $this->nonce_name ) ) {
            return response( 'Invalid nonce.', 403 );
        }

        return $handler->handle( $request );
    }
}
