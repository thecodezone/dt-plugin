<?php

/**
 * @var RouteCollectionInterface $r
 */

use DT\Plugin\CodeZone\WPSupport\Middleware\HasCap;
use DT\Plugin\CodeZone\WPSupport\Middleware\Nonce;
use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\League\Route\RouteCollectionInterface;

$r->group( '/wp-admin', function ( RouteCollectionInterface $r ) {

	$r->get( '/admin.php?page=dt-plugin', [ GeneralSettingsController::class, 'show' ] );

	$r->get( '/admin.php?page=dt-plugin&tab=general', [ GeneralSettingsController::class, 'show' ] );

	$r->post( '/admin.php?page=dt-plugin&tab=general', [
		GeneralSettingsController::class,
		'update'
	] )->middleware( new Nonce( 'dt_admin_form_nonce' ) );

} )->middleware( new HasCap( 'manage_dt' ) );
