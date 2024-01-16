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
use CodeZone\Bible\Controllers\Admin\GeneralSettingsController;
use CodeZone\Bible\Controllers\HelloController;
use CodeZone\Bible\Controllers\StarterMagicLink\HomeController;
use CodeZone\Bible\Controllers\StarterMagicLink\SubpageController;
use CodeZone\Bible\Controllers\UserController;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;


$r->condition( 'plugin', function ( $r ) {
	$r->group( 'codezone/bible', function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );
		$r->get( '/users/{id}', [ UserController::class, 'show', [ 'middleware' => [ 'auth', 'can:list_users' ] ] ] );
		$r->get( '/me', [ UserController::class, 'current', [ 'middleware' => 'auth' ] ] );
	} );

	$r->group( 'codezone/bible/api', function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );
		$r->get( '/{path:.*}', fn( Request $request, Response $response ) => $response->setStatusCode( 404 ) );
	} );
} );

$r->condition( 'backend', function ( Routes $r ) {
	$r->middleware( 'can:manage_dt', function ( Routes $r ) {
		$r->group( 'wp-admin/admin.php', function ( Routes $r ) {
			$r->get( '?page=bible-reader', [ GeneralSettingsController::class, 'show' ] );
			$r->get( '?page=bible-reader&tab=general', [ GeneralSettingsController::class, 'show' ] );

			$r->middleware( 'nonce:dt_admin_form_nonce', function ( Routes $r ) {
				$r->post( '?page=bible-reader', [ GeneralSettingsController::class, 'update' ] );
				$r->post( '?page=bible-reader&tab=general', [ GeneralSettingsController::class, 'update' ] );
			} );
		} );
	} );
} );

$r->middleware( 'magic:starter/app', function ( Routes $r ) {
	$r->group( 'starter/app/{key}', function ( Routes $r ) {
		$r->get( '', [ HomeController::class, 'show' ] );

		// Remember to add any magic link routes to the actions array in the magic link class,
		// otherwise they will be blocked.
		$r->get( '/subpage', [ SubpageController::class, 'show' ] );
	} );
} );
