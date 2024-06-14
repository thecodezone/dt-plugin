<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\Controllers\Admin\GeneralSettingsController;

$r->group( 'wp-admin/dt-plugin', function (\DT\Plugin\League\Route\RouteGroup $r ) {
    $r->get( '/', [ GeneralSettingsController::class, 'show' ] );
    $r->get( 'general', [ GeneralSettingsController::class, 'show' ] );
    $r->post( 'general', [ GeneralSettingsController::class, 'update' ] );
} )->middlewares( [
   //new Can( "manage_dt")a
]);
