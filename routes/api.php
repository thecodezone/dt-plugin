<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;

$r->get( '/hello', [ HelloController::class, 'show' ] );
$r->get( '/users/{id}', [ UserController::class, 'data' ] );
