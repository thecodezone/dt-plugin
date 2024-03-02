<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
use CodeZone\Bible\Services\BibleBrains\Services\Languages;
use Exception;
use function CodeZone\Bible\set_option;
use function CodeZone\Bible\transaction;
use function CodeZone\Bible\validate;
use function CodeZone\Bible\view;


/**
 * Class BibleBrainsController
 *
 * This class is responsible for handling the BibleBrains settings and API authorization.
 */
class BibleBrainsFormController {
	/**
	 * Show the settings page.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 */
	public function show( Request $request, Response $response, Languages $languages, Bibles $bibles ) {
		$tab = "bible";

		//BibleBrains
		$bible_plugin_bible_brains_key = get_option( 'bible_plugin_bible_brains_key', defined( 'BIBLE_BRAINS_KEY' ) ? BP_BIBLE_BRAINS_KEY : '' );

		if ( ! $bible_plugin_bible_brains_key ) {
			return $this->validation_form( $request, $response );
		}
		try {
			$validate = $bibles->media_types();
		} catch ( Exception $e ) {
			return $this->validation_form( $request, $response );
		}

		if ( $validate->status() === 401 ) {
			return $this->validation_form( $request, $response );
		}

		//Languages
		$bible_plugin_languages = get_option( 'bible_plugin_languages', '6414' );
		$selected_language_ids  = explode( ',', $bible_plugin_languages );
		$bible_plugin_language  = get_option( 'bible_plugin_language', Arr::first( $selected_language_ids ) );
		$selected_languages     = $selected_language_ids ? $languages->find_many( $selected_language_ids )['data'] : [];
		$language_options       = $languages->as_options( $selected_languages );

		//Bibles
		$bible_plugin_bibles = get_option( 'bible_plugin_bibles' );
		if ( ! $bible_plugin_bibles ) {
			$bible_plugin_bibles = implode( ',', $bibles->default_for_languages( Arr::pluck( $selected_languages, 'codes.Iso 639-2' ) )['data'] );
		}
		$selected_bible_ids = explode( ',', $bible_plugin_bibles );
		$bible_plugin_bible = Arr::first( $selected_bible_ids );
		$selected_bibles    = $selected_bible_ids ? $bibles->find_many( $selected_bible_ids )['data'] : [];
		$bible_options      = $bibles->as_options( $selected_bibles );

		//Media
		$bible_plugin_media_types = get_option( 'bible_plugin_media_types', 'text_plain' );
		$media_type_options       = $bibles->media_type_options()["data"];

		$fields = compact( 'bible_plugin_bible_brains_key', 'bible_plugin_languages', 'bible_plugin_language', 'bible_plugin_bibles', 'bible_plugin_bible', 'bible_plugin_media_types' );
		$nonce  = wp_create_nonce( 'bible-plugin' );

		$action                    = '/bible/api/bible-brains';
		$language_options_endpoint = '/bible/api/languages/options';
		$bible_options_endpoint    = '/bible/api/languages/{id}/bibles/options';

		return view( "settings/bible-brains-form", [
			'action'                    => $action,
			'tab'                       => $tab,
			'language_options_endpoint' => $language_options_endpoint,
			'language_options'          => $language_options,
			'bible_options'             => $bible_options,
			'bible_options_endpoint'    => $bible_options_endpoint,
			'media_type_options'        => $media_type_options,
			'fields'                    => $fields,
			'nonce'                     => $nonce,
		] );
	}

	/**
	 * This method generates a form for validating a given request and response.
	 *
	 * @param Request $request The request object containing the form data.
	 * @param Response $response The response object for rendering the form.
	 *
	 * @return View A view containing the form data.
	 */
	public function validation_form( Request $request, Response $response ) {
		$tab = "bible";

		$bible_plugin_bible_brains_key = get_option( 'bible_plugin_bible_brains_key', defined( 'BIBLE_BRAINS_KEY' ) ? BP_BIBLE_BRAINS_KEY : '' );
		$fields                        = compact( 'bible_plugin_bible_brains_key' );
		$nonce                         = wp_create_nonce( 'bible-plugin' );

		return view( "settings/bible-brains-key-form", [
			'tab'    => $tab,
			'fields' => $fields,
			'nonce'  => $nonce,
		] );
	}

	/**
	 * Submit the request and return either success or error message.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 *
	 * @return mixed Returns success with the key if random number is 1, otherwise returns error message.
	 * @throws Exception
	 */
	public function submit( Request $request, Response $response, Bibles $bibles ) {
		$errors = validate( $request->post(), [
			'bible_plugin_bible_brains_key' => 'required',
			'bible_plugin_languages'        => 'required',
			'bible_plugin_language'         => 'required',
			'bible_plugin_bibles'           => 'required',
			'bible_plugin_bible'            => 'required',
			'bible_plugin_media_types'      => 'required',
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please complete the required fields.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$validation_response = $this->validate( $response, $request, $bibles );

		if ( ! $validation_response->isOk() ) {
			return $validation_response;
		}

		$result = transaction( function () use ( $request ) {
			set_option( 'bible_plugin_bible_brains_key', $request->post( 'bible_plugin_bible_brains_key' ) );
			set_option( 'bible_plugin_languages', $request->post( 'bible_plugin_languages' ) );
			set_option( 'bible_plugin_language', $request->post( 'bible_plugin_language' ) );
			set_option( 'bible_plugin_bibles', $request->post( 'bible_plugin_bibles' ) );
			set_option( 'bible_plugin_bible', $request->post( 'bible_plugin_bible' ) );
			set_option( 'bible_plugin_media_types', $request->post( 'bible_plugin_media_types' ) );
		} );

		if ( ! $result === true ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error' => __( 'Form could not be submitted.', 'bible-plugin' ),
			] );
		}

		return $response->setContent( [
			'success' => true,
		] );
	}

	/**
	 * Authorize the API key
	 *
	 * @param Response $response The HTTP response object.
	 *
	 * @return array|Response An array with 'success' key if the API key is valid, or a Response object with an error message if the API key is invalid.
	 */
	public function validate( Response $response, Request $request, Bibles $bibles ) {
		$errors = validate( $request->post(), [
			'bible_plugin_bible_brains_key' => 'required',
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please enter a key.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$bibleBrainsResponse = $bibles->find( 'ENGESV', [ 'key' => $request->get( 'bible_plugin_bible_brains_key' ) ] );

		if ( $bibleBrainsResponse->status() !== 200 ) {
			return $response->setStatusCode( 401 )->setContent( [
				'error'  => __( 'Failed to validate key.', 'bible-plugin' ),
				'errors' => [
					'bible_plugin_bible_brains_key' => __( 'Invalid.', 'bible-plugin' ),
				],
			] );
		}

		$result = transaction( function () use ( $request ) {
			set_option( 'bible_plugin_bible_brains_key', $request->post( 'bible_plugin_bible_brains_key' ) );
		} );

		if ( ! $result === true ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error' => __( 'Form could not be submitted.', 'bible-plugin' ),
			] );
		}

		return $response->setContent( [
			'success' => true,
		] );
	}
}
