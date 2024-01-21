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
 * @var Routes $r
 * @see https://github.com/thecodezone/wp-router
 */

use CodeZone\Bible\CodeZone\Router\FastRoute\Routes;
use CodeZone\Bible\Controllers\Admin\BibleBrainsController;
use CodeZone\Bible\Controllers\Admin\CustomizationController;
use CodeZone\Bible\Controllers\Admin\SupportController;
use CodeZone\Bible\Controllers\MediaController;
use CodeZone\Bible\Controllers\VersionController;
use CodeZone\Bible\Plugin;


$r->condition( 'plugin', function ( $r ) {
	$r->group( Plugin::$home_route, function ( Routes $r ) {
	} );

	$r->group( Plugin::$home_route . '/api', function ( Routes $r ) {
		$r->get( '/versions', [ VersionController::class, 'index' ] );
		$r->get( '/media', [ MediaController::class, 'index' ] );

		$r->middleware( [ 'can:manage_options', 'nonce:bible_reader' ], function ( Routes $r ) {
			$r->post( '/bible-brains/authorize', [ BibleBrainsController::class, 'authorize' ] );
			$r->post( '/bible-brains', [ BibleBrainsController::class, 'submit' ] );
		} );
	} );
} );

$r->condition( 'backend', function ( Routes $r ) {
	$r->middleware( 'can:manage_options', function ( Routes $r ) {
		$r->group( 'wp-admin/admin.php', function ( Routes $r ) {
			$r->get( '?page=bible-reader', [ BibleBrainsController::class, 'show' ] );
			$r->get( '?page=bible-reader&tab=bible', [ BibleBrainsController::class, 'show' ] );
			$r->get( '?page=bible-reader&tab=customization', [ CustomizationController::class, 'show' ] );
			$r->get( '?page=bible-reader&tab=support', [ SupportController::class, 'show' ] );
		} );
	} );
} );
