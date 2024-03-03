<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
use CodeZone\Bible\Services\BibleBrains\Services\Languages;
use function CodeZone\Bible\collect;

/**
 * Class LanguageController
 *
 * The LanguageController class is responsible for handling language-related requests
 */
class LanguageBibleController {

	/**
	 * Retrieve all bibles for a language.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 */
	public function index( Request $request, Response $response, $id, Languages $languages ) {
		$ids    = explode( ',', $id );
		$result = collect( $languages->find_many( $ids ) );

		$payload = [ 'data' => [] ];
		if ( ! count( $result->get( 'data' ) ) ) {
			return $response->setStatusCode( 404 );
		}

		$languages = collect( $result->get( 'data', [] ) );

		foreach ( $languages as $language ) {
			array_push( $payload['data'], ...$language['bibles'] );
		}

		$payload['data'] = collect( $payload['data'] )->unique( 'id' );

		return $response->setContent( $payload );
	}

	public function options( Request $request, Response $response, $id, Languages $languages, Bibles $bibles ) {
		$result = $this->index( $request, $response, $id, $languages );
		if ( ! $result->isOk() ) {
			return $result;
		}

		$result = $result->getOriginalContent();

		$result['data'] = $bibles->as_options( $result['data'] ?? [] );

		return $response->setContent( $result );
	}
}
