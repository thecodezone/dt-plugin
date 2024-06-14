<?php

/**
 * @var \DT\Plugin\League\Route\RouteGroup $r
 */

use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;

$r->get( '/hello', [ HelloController::class, 'data' ] );
$r->get( '/users/{id}', [ UserController::class, 'data' ] );
