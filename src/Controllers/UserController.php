<?php

namespace DT\Plugin\Controllers;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Services\Template;
use function DT\Plugin\response;
use function DT\Plugin\template;

class UserController {

    /**
     * Fetches and returns the details of a user.
     *
     * @param ServerRequestInterface $request The request object.
 */
    public function data( ServerRequestInterface $request, $params ) {
        $id = sanitize_text_field( wp_unslash( $params['id'] ) );
        $user = get_user_by( 'id', $id );

        if ( ! $user ) {
            return response( 'User not found', 404 );
        }

        return response([
            'user' => $user
        ]);
    }
    /**
     * You can also return a string or array from a controller method,
     * it will be automatically added to the response object.
     *
     * @param ServerRequestInterface $request The request object.
     */
    public function current( ServerRequestInterface $request ) {

        if ( ! is_user_logged_in() ) {
            return response( 'User not logged in', 401 );
        }

        return template( 'user', [
            'user' => wp_get_current_user()
        ] );
    }

    /**
     * Fetches and displays the details of a user.
     *
     * @param ServerRequestInterface $request The request object.
     * @param arary $params The route parameters.
     */
    public function show( ServerRequestInterface $request, $params ) {
        $id  = sanitize_text_field( wp_unslash( $params['id'] ) );
        $user = get_user_by( 'id', $id );

        if ( ! $user ) {
            return response( 'User not found', 404 );
        }

        return template( 'user', [
            'user' => $user
        ] );
    }
}
