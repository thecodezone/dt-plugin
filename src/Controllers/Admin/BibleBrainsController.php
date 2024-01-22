<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
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
	public function show( Request $request, Response $response ) {
		$tab              = "bible";
		$language_options = [
			'eng' => 'English',
			'es'  => 'Spanish',
			'fr'  => 'French',
			'de'  => 'German',
			'it'  => 'Italian',
			'pt'  => 'Portuguese',
			'ru'  => 'Russian',
			'zh'  => 'Chinese',
		];
		$version_options  = [
			'ENGKJV' => 'King James Version',
		];
		$media_options    = [
			'audio' => 'Audio',
			'video' => 'Video',
			'text'  => 'Text',
		];
		$old              = [
			'bible_plugin_bible_brains_key' => get_option( 'bible_plugin_bible_brains_key', 'fake' ),
			'bible_plugin_languages'        => get_option( 'bible_plugin_languages', 'eng' ),
			'bible_plugin_language'         => get_option( 'bible_plugin_language', array_key_first( $language_options ) ),
			'bible_plugin_versions'         => get_option( 'bible_plugin_versions', array_key_first( $version_options ) ),
			'bible_plugin_version'          => get_option( 'bible_plugin_version', array_key_first( $version_options ) ),
			'bible_plugin_media'            => get_option( 'bible_plugin_media', implode( ',', array_keys( $media_options ) ) ),
		];
		$error            = __( 'An error has occurred.', 'bible-plugin' );
		$success          = __( 'Saved.', 'bible-plugin' );
		$nonce            = wp_create_nonce( 'bible_plugin' );

		return view( "settings/bible-brains", [
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
	 * Authorize the API key
	 *
	 * @param Request $request The HTTP request object.
	 * @param Response $response The HTTP response object.
	 *
	 * @return array|Response An array with 'success' key if the API key is valid, or a Response object with an error message if the API key is invalid.
	 */
	public function authorize( Request $request, Response $response ) {
		return random_int( 0, 1 ) ? [
			'success' => $request->get( 'key' ),
		] : $response->setStatusCode( 400 )->setContent( [
			'error' => __( 'Invalid API key', 'bible-plugin' )
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
	public function submit( Request $request, Response $response ) {
		global $wpdb;

		$errors = validate( $request->post(), [
			'bible_plugin_languages' => 'required',
			'bible_plugin_language'  => 'required',
			'bible_plugin_versions'  => 'required',
			'bible_plugin_version'   => 'required',
			'bible_plugin_media'     => 'required',
		] );


		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( [
				'error'  => __( 'Please complete the required fields.', 'bible-plugin' ),
				'errors' => $errors,
			] );
		}

		$result = transaction( function () use ( $request ) {
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
}
