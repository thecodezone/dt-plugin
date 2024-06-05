<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

class DeprecationErrorsMiddleware implements Middleware {

    public function handle(Request $request, Response $response, callable $next)
    {
        //TODO: Remove this. We should probably move away from using Laravel request classes if we are going to support PHP 7.4 and PHP 8.3.
        error_reporting(error_reporting() ^ E_DEPRECATED);

        return $next($request, $response);
    }
}
