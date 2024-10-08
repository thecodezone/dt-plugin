<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Psr\Http\Server\MiddlewareInterface;
use DT\Plugin\Psr\Http\Server\RequestHandlerInterface;
use function DT\Plugin\redirect;

class LoggedIn implements MiddlewareInterface {
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        if ( ! is_user_logged_in() ) {
            return redirect( wp_login_url( $request->getUri() ) );
        }

        return $handler->handle( $request );
    }
}
