<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use Exception;
use function CodeZone\Bible\transaction;
use function CodeZone\Bible\validate;
use function CodeZone\Bible\view;
use function CodeZone\Bible\get_plugin_option;
use function CodeZone\Bible\set_plugin_option;

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
	public function show( Request $request, Response $response, Languages $language_service, Bibles $bible_service ) {
		$tab              = "bible";
		$bible_brains_key = get_plugin_option( 'bible_brains_key' );
		if ( ! $bible_brains_key ) {
			return $this->validation_form( $request, $response );
		}

		try {
			$bible_service->media_types();
		} catch ( BibleBrainsException $e ) {
			return $this->validation_form( $request, $response, $e->getMessage() );
		}

		try {
			//Languages
			$languages             = get_plugin_option( 'languages' );
			$selected_language_ids = explode( ',', $languages );
			$language              = get_plugin_option( 'language', Arr::first( $selected_language_ids ) );
			$selected_languages    = $selected_language_ids ? $language_service->find_many( $selected_language_ids )['data'] : [];
			$language_options      = $language_service->as_options( $selected_languages );

			//Bibles
			$bibles = get_plugin_option( 'bibles', false, true );
			if ( ! $bibles ) {
				$bibles = implode( ',', $bible_service->default_for_languages( Arr::pluck( $selected_languages, 'codes.Iso 639-2' ) )['data'] );
			}
			$selected_bible_ids = explode( ',', $bibles );
			$selected_bibles    = $selected_bible_ids ? $bible_service->find_many( $selected_bible_ids )['data'] : [];
			$bible_options      = $bible_service->as_options( $selected_bibles );

			//Media
			$media_types        = get_plugin_option( 'media_types' );
			$media_type_options = $bible_service->media_type_options()["data"];
		} catch ( Exception $e ) {
			return $this->validation_form( $request, $response, $e->getMessage() );
		}

		$fields = compact( 'bible_brains_key', 'languages', 'language', 'bibles', 'media_types' );
		$nonce  = wp_create_nonce( 'bible-plugin' );

		$action                    = '/bible/api/bible-brains';
		$language_options_endpoint = '/bible/api/languages/options';
		$bible_options_endpoint    = '/bible/api/bibles/options';

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
	public function validation_form( Request $request, Response $response, $error = "" ) {
		$tab = "bible";

		$bible_brains_key = get_plugin_option( 'bible_brains_key' );
		$fields           = compact( 'bible_brains_key' );
		$nonce            = wp_create_nonce( 'bible-plugin' );
		$error            = $error ?? "";

		return view( "settings/bible-brains-key-form", [
			'tab'    => $tab,
			'fields' => $fields,
			'nonce'  => $nonce,
			'error'  => $error,
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
			'languages'   => 'required',
			'language'    => 'required',
			'bibles'      => 'required',
			'media_types' => 'required',
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please complete the required fields.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$result = transaction( function () use ( $request ) {
			set_plugin_option( 'bible_brains_key', $request->post( 'bible_brains_key' ) );
			set_plugin_option( 'languages', $request->post( 'languages' ) );
			set_plugin_option( 'language', $request->post( 'language' ) );
			set_plugin_option( 'bibles', $request->post( 'bibles' ) );
			set_plugin_option( 'media_types', $request->post( 'media_types' ) );
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
			'bible_brains_key' => 'required',
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please enter a key.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$key = $request->input( 'bible_brains_key' );
		try {
			$bibles->find( 'ENGESV', [ 'key' => $key, 'cache' => false ] );
		} catch ( BibleBrainsException $e ) {
			return $response->setStatusCode( 401 )->setContent( [
				'error'  => __( 'Failed to validate key.', 'bible-plugin' ),
				'errors' => [
					'bible_brains_key' => __( 'Invalid.', 'bible-plugin' ),
				],
			] );
		}

		$result = transaction( function () use ( $request ) {
			set_plugin_option( 'bible_brains_key', $request->post( 'bible_brains_key' ) );
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
