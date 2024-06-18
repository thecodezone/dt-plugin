<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\CodeZone\WPSupport\Middleware\HasCap;
use DT\Plugin\CodeZone\WPSupport\Middleware\Nonce;
use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\League\Route\RouteGroup;

$r->group( '', function ( RouteGroup $r ) {
    $r->get( 'general', [ GeneralSettingsController::class, 'show' ] );
    $r->post( 'general', [ GeneralSettingsController::class, 'update' ] )->middleware( new Nonce('dt_admin_form_nonce') );
} )->middleware( new HasCap( 'manage_dt' ) );


