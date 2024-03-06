<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\RedirectResponse;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use function CodeZone\Bible\transaction;
use function CodeZone\Bible\validate;
use function CodeZone\Bible\view;
use function CodeZone\Bible\get_plugin_option;
use function CodeZone\Bible\set_plugin_option;


class CustomizationFomController {
	/**
	 * Display the customization settings page.
	 *
	 * @param Request $request The HTTP request object.
	 * @param Response $response The HTTP response object.
	 *
	 * @return String The view containing the customization settings page.
	 */
	public function show( Request $request, Response $response ) {
		$tab                  = "customization";
		$nonce                = wp_create_nonce( 'bible-brains' );
		$color_scheme_options = [
			[
				'itemText' => __( 'Light', 'bible-plugin' ),
				'value'    => 'light',
			],
			[
				'itemText' => __( 'Dark', 'bible-plugin' ),
				'value'    => 'dark',
			]
		];
		$fields               = [
			'color_scheme' => get_plugin_option( 'color_scheme', false, true ),
			'colors'       => get_plugin_option( 'colors', false, true )
		];

		return $response->setcontent(
			view( "settings/customization-form",
				compact( 'tab', 'nonce', 'color_scheme_options', 'fields' )
			)
		);
	}

	/**
	 * Submit the general settings admin tab form
	 */
	public function submit( Request $request, Response $response ) {
		$errors = validate( $request->post(), [
			'color_scheme' => 'required',
			'colors'       => 'required'
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please complete the required fields.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$result = transaction( function () use ( $request ) {
			set_plugin_option( 'color_scheme', $request->post( 'color_scheme' ) );
			set_plugin_option( 'colors', $request->post( 'colors' ) );
		} );

		if ( ! $result === true ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error' => __( 'Form could not be submitted.', 'bible-plugin' ),
			] );
		}

		return $response->setContent( [
			'success' => true,
		] );

		return new RedirectResponse( 302, admin_url( 'admin.php?page=bible-plugin&tab=general&updated=true' ) );
	}
}
