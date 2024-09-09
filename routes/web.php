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
 * @var RouteCollectionInterface $r
 * @see https://github.com/thecodezone/wp-router
 */

use DT\Plugin\CodeZone\WPSupport\Middleware\HasCap;
use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\League\Route\RouteCollectionInterface;
use DT\Plugin\Middleware\LoggedIn;

$r->group( '', function ( RouteCollectionInterface $r ) {

    $r->get( '/hello', [ HelloController::class, 'show' ] );

    $r->get( '/users/me', [ UserController::class, 'current' ] );

    $r->get( '/users/{id}', [ UserController::class, 'show' ] )
	  ->middleware( new HasCap( 'dt_list_users' ) );
} )->middleware( new LoggedIn() );
