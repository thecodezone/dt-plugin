<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\League\Route\RouteGroup;

$r->get( 'general', [ GeneralSettingsController::class, 'show' ] );
$r->post( 'general', [ GeneralSettingsController::class, 'update' ] );
