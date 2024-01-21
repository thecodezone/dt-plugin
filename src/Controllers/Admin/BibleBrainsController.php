<?php

namespace CodeZone\Bible\Controllers\Admin;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
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
			'bible_reader_bible_brains_key' => get_option( 'bible_reader_bible_brains_key', 'fake' ),
			'bible_reader_languages'        => get_option( 'bible_reader_languages', 'eng' ),
			'bible_reader_language'         => get_option( 'bible_reader_language', array_key_first( $language_options ) ),
			'bible_reader_versions'         => get_option( 'bible_reader_versions', array_key_first( $version_options ) ),
			'bible_reader_version'          => get_option( 'bible_reader_version', array_key_first( $version_options ) ),
			'bible_reader_media'            => get_option( 'bible_reader_media', implode( ',', array_keys( $media_options ) ) ),
		];
		$error            = __( 'An error has occurred.', 'bible-reader' );
		$success          = __( 'Saved.', 'bible-reader' );
		$nonce            = wp_create_nonce( 'bible_reader' );

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
			'error' => __( 'Invalid API key', 'bible-reader' )
		] );
	}

	/**
	 * Submit the request and return either success or error message.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 *
	 * @return mixed Returns success with the key if random number is 1, otherwise returns error message.
	 */
	public function submit( Request $request, Response $response ) {
		return [
			'success' => true,
		];
	}
}
