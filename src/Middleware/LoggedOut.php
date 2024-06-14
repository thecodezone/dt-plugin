<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Plugin;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Psr\Http\Server\MiddlewareInterface;
use DT\Plugin\Psr\Http\Server\RequestHandlerInterface;
use function DT\Plugin\redirect;
use function DT\Plugin\route_url;

class LoggedOut implements MiddlewareInterface {

    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        if ( is_user_logged_in() ) {
            return redirect( route_url() );
        }

        return $handler->handle( $request );
    }
}
