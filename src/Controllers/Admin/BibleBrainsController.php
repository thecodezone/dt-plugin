<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
use CodeZone\Bible\Services\BibleBrains\Services\Languages;
use function CodeZone\Bible\set_option;
use function CodeZone\Bible\transaction;
use function CodeZone\Bible\validate;
use function CodeZone\Bible\view;


/**
 * Class BibleBrainsController
 *
 * This class is responsible for handling the BibleBrains settings and API authorization.
 */
class BibleBrainsController {
	/**
	 * Show the settings page.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 */
	public function show( Request $request, Response $response, Languages $languages ) {
		$tab = "bible";

		$language_options = collect( $languages->all()['data'] ?? [] )->pluck( 'name', 'id' )->sort()->toArray();
		$version_options  = [
			'ENGKJV' => 'King James Version',
		];
		$media_options    = [
			'audio' => 'Audio',
			'video' => 'Video',
			'text'  => 'Text',
		];
		$old              = [
			'bible_plugin_bible_brains_key' => get_option( 'bible_plugin_bible_brains_key', defined( 'BIBLE_BRAINS_KEY' ) ? BP_BIBLE_BRAINS_KEY : '' ),
			'bible_plugin_languages'        => get_option( 'bible_plugin_languages', 'eng' ),
			'bible_plugin_language'         => get_option( 'bible_plugin_language', array_key_first( $language_options ) ),
			'bible_plugin_versions'         => get_option( 'bible_plugin_versions', array_key_first( $version_options ) ),
			'bible_plugin_version'          => get_option( 'bible_plugin_version', array_key_first( $version_options ) ),
			'bible_plugin_media'            => get_option( 'bible_plugin_media', implode( ',', array_keys( $media_options ) ) ),
		];
		$error            = __( 'An error has occurred.', 'bible-plugin' );
		$success          = __( 'Saved.', 'bible-plugin' );
		$nonce            = wp_create_nonce( 'bible-plugin' );
		$action           = '/bible/api/bible-brains';
		$key_action       = '/bible/api/bible-brains/key';

		return view( "settings/bible-brains", [
			'action'           => $action,
			'key_action'       => $key_action,
			'tab'              => $tab,
			'language_options' => $language_options,
			'version_options'  => $version_options,
			'media_options'    => $media_options,
			'old'              => $old,
			'error'            => $error,
			'success'          => $success,
			'nonce'            => $nonce,
		] );
	}

	/**
	 * Submit the request and return either success or error message.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 *
	 * @return mixed Returns success with the key if random number is 1, otherwise returns error message.
	 * @throws \Exception
	 */
	public function submit( Request $request, Response $response, Bibles $bibles ) {
		$errors = validate( $request->post(), [
			'bible_plugin_bible_brains_key' => 'required',
			'bible_plugin_languages'        => 'required',
			'bible_plugin_language'         => 'required',
			'bible_plugin_versions'         => 'required',
			'bible_plugin_version'          => 'required',
			'bible_plugin_media'            => 'required',
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please complete the required fields.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$result = $this->validate( $response, $request, $bibles );

		if ( ! is_array( $result ) || empty( $result['success'] ) ) {
			return $result;
		}

		$result = transaction( function () use ( $request ) {
			set_option( 'bible_plugin_bible_brains_key', $request->post( 'bible_plugin_bible_brains_key' ) );
			set_option( 'bible_plugin_languages', $request->post( 'bible_plugin_languages' ) );
			set_option( 'bible_plugin_language', $request->post( 'bible_plugin_language' ) );
			set_option( 'bible_plugin_versions', $request->post( 'bible_plugin_versions' ) );
			set_option( 'bible_plugin_version', $request->post( 'bible_plugin_version' ) );
			set_option( 'bible_plugin_media', $request->post( 'bible_plugin_media' ) );
		} );

		if ( ! $result === true ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error' => __( 'Form could not be submitted.', 'bible-plugin' ),
			] );
		}

		return [
			'success' => true,
		];
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

		$bibleBrainsResponse = $bibles->copyright( 'ENGESV', [ 'key' => $request->get( 'bible_plugin_bible_brains_key' ) ] );

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

		return [
			'success' => true,
		];
	}
}
