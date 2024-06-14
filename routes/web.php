<?php
/**
 * Conditions are used to determine if a group of routes should be registered.
 *
 * Groups are used to register a group of routes with a common URL prefix.
 *
 * Middleware is used to modify requests before they are handled by a controller, or to modify responses before they are returned to the client.
 *
 * Routes are used to bind a URL to a controller.
 *
 * @var \DT\Plugin\League\Route\Router $r
 * @see https://github.com/thecodezone/wp-router
 */

use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\League\Route\RouteGroup;
use DT\Plugin\Middleware\LoggedIn;

$r->group( '', function ( RouteGroup $r ) {
    $r->get( '/hello', [ HelloController::class, 'show' ] );
    $r->get( '/users/{id}', [ UserController::class, 'show' ] );
        //->middleware( new Can( 'list-users') );
} )->middleware( new LoggedIn() );
