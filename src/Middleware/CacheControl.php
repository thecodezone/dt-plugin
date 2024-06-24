<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

class CacheControl implements Middleware {

    protected $value;

    public function handle(Request $request, Response $response, callable $next)
    {
        $response->headers->set( 'Cache-Control', 'uncached' );

        return $next( $request, $response );
    }
}
