<?php

namespace DT\Plugin\Controllers\Admin;

use DT\Plugin\Nette\Schema\Expect;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\get_plugin_option;
use function DT\Plugin\set_plugin_option;
use function DT\Plugin\transaction;
use function DT\Plugin\redirect;
use function DT\Plugin\validate;
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

        $body = $request->getParsedBody();

        $option = get_plugin_option( 'option' );
        $another_option = get_plugin_option( 'another_option' );

		return view( "settings/general", compact( 'tab', 'link', 'page_title', 'option', 'another_option' ) );
	}

	/**
	 * Submit the general settings admin tab form
     * @throws \Exception
	 */
	public function update( ServerRequestInterface $request ) {
		$error = false;

		// Add the settings update code here
        $body = $request->getParsedBody();

        $validation_result = validate( [
            'option' => Expect::string()->required(),
            'another_option' => Expect::string()->required(),
        ], $body );

        if ( $validation_result !== true ) {
            $error = $validation_result;
        }

		if ( ! $error ) {

            $option = sanitize_text_field( wp_unslash( $body['option'] ) );
            $another_option = sanitize_text_field( wp_unslash( $body['another_option'] ) );

            try {
                $result = transaction( function () use ( $option, $another_option ) {
                    set_plugin_option( 'option', $option );
                    set_plugin_option( 'another_option', $another_option );
                } );

                if ( $result !== true ) {
                    $error = __( 'The form could not be submitted.', 'dt-plugin' );
                }
            } catch ( \Exception $e ) {
                $error = $e->getMessage();
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
