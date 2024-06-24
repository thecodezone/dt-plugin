<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;

class SetBypassCookie implements Middleware {

    protected $value;

    public function get_bypass_value() {
        return md5( session_id() . wp_get_session_token() . time() );
    }

    public function handle(Request $request, Response $response, callable $next)
    {
        if (! is_admin() ) {
            setcookie( 'BIBLE_PLUGIN', $this->get_bypass_value(), time() + 600, '/');
        }

        return $next( $request, $response );
    }
}
