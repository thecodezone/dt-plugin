<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use function CodeZone\Bible\view;

class CustomizationController {
	/**
	 * Show the general settings admin tab
	 */
	public function show( Request $request, Response $response ) {
		$tab = "customization";

		return view( "settings/customization", compact( 'tab' ) );
	}

	/**
	 * Submit the general settings admin tab form
	 */
	public function update( Request $request, Response $response ) {

		// Add the settings update code here

		return new RedirectResponse( 302, admin_url( 'admin.php?page=bible-plugin&tab=general&updated=true' ) );
	}
}
