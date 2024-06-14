<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Psr\Http\Server\MiddlewareInterface;
use DT\Plugin\Psr\Http\Server\RequestHandlerInterface;
use DT_Magic_Url_Base;
use function DT\Plugin\container;

/**
 * Check if the current path is a magic link path.
 */
class MagicLink implements MiddlewareInterface {
	protected DT_Magic_Url_Base $magic_link;

	/**
	 * Construct a new instance of the class.
	 *
	 * @param DT_Magic_Url_Base|string $magic_link The magic link instance or the class name.
	 *
	 * @return void
	 */
	public function __construct( $magic_link ) {
		if ( is_string( $magic_link ) ) {
			$magic_link = container()->get( $magic_link );
		}
		$this->magic_link = $magic_link;
	}

	public function handle( Request $request, Response $response, callable $next ) {
		if ( ! $this->magic_link || ! $this->magic_link->check_parts_match() ) {
			$response->setStatusCode( 404 );
		}

		return $next( $request, $response );
	}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method.
    }
}
