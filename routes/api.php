<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\League\Route\RouteGroup;
use DT\Plugin\Middleware\HasCap;

$r->group( '', function ( RouteGroup $r ) {
    $r->get( '/hello', [ HelloController::class, 'show' ] );
    $r->get( '/users/{id}', [ UserController::class, 'show' ] );
} )->middleware( new HasCap( 'manage_dt' ) );
