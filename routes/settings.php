<?php

/**
 * @var \DT\Plugin\League\Route\Router $r
 */

use DT\Plugin\CodeZone\WPSupport\Middleware\Nonce;
use DT\Plugin\Controllers\Admin\GeneralSettingsController;

$r->get( 'general', [ GeneralSettingsController::class, 'show' ] );
$r->post( 'general', [ GeneralSettingsController::class, 'update' ] )->middleware( new Nonce('dt_admin_form_nonce') );
