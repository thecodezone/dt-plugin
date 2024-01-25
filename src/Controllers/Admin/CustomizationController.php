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
		$tab                  = "customization";
		$action               = '/bible/api/bible-brains';
		$method               = 'POST';
		$nonce                = wp_create_nonce( 'bible-brains' );
		$color_scheme_options = [
			'lighter' => __( 'Lighter', 'bible-brains' ),
			'light'   => __( 'Light', 'bible-brains' ),
			'dark'    => __( 'Dark', 'bible-brains' ),
			'darker'  => __( 'Darker', 'bible-brains' ),
		];
		$old                  = [
			'bible_brains_color_scheme'        => get_option( 'bible_brains_scheme', 'lighter' ),
			'bible_brains_header_color'        => get_option( 'bible_brains_header_color' ),
			'bible_brains_header_color_custom' => get_option( 'bible_brains_header_color_custom' ),
			'bible_brains_footer_color'        => get_option( 'bible_brains_header_color' ),
			'bible_brains_footer_color_custom' => get_option( 'bible_brains_header_color_custom' ),
		];

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
