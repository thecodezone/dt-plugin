<?php

namespace DT\Plugin\Controllers\Admin;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\transaction;
use function DT\Plugin\redirect;
use function DT\Plugin\view;
use function DT\Plugin\set_option;


class GeneralSettingsController {
	/**
	 * Show the general settings admin tab
	 */
	public function show( ServerRequestInterface $request ) {
		$tab        = "general";
		$link       = 'settings.php?page=dt_plugin&tab=';
		$page_title = "DT Plugin Settings";

		return view( "settings/general", compact( 'tab', 'link', 'page_title' ) );
	}

	/**
	 * Submit the general settings admin tab form
	 */
	public function update( ServerRequestInterface $request ) {
		$error = false;

		// Add the settings update code here
        $body = $request->getParsedBody();

        if ( ! isset( $body['option1'] ) || ! isset( $body['option2'] ) ) {
            $error = __( 'Please complete the required fields.', 'dt-plugin' );
        }

        $option_1 = sanitize_text_field( wp_unslash( $body['option1'] ) );
        $option_2 = sanitize_text_field( wp_unslash( $body['option2'] ) );

		if ( ! $error ) {
			//Perform update in a MYSQL transaction
			$result = transaction( function () use ( $option_1, $option_2 ) {
				set_option( 'option1', $option_1 );
				set_option( 'option2', $option_2 );
			} );

			if ( $result !== true ) {
				$error = __( 'The form could not be submitted.', 'dt-plugin' );
			}
		}


		if ( $error ) {
			return redirect( admin_url( 'settings.php?page=dt_plugin&tab=general&' . http_build_query( [
                    'error'  => $error
                ] )
			) );
		}

		return redirect( admin_url( 'settings.php?page=dt_plugin&tab=general&updated=true' ) );
	}
}
