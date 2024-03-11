<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use function CodeZone\Bible\collect;

/**
 * Class LanguageController
 *
 * The LanguageController class is responsible for handling language-related requests
 */
class LanguageController {
	/**
	 * Retrieve a language.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param Languages $languages The Languages instance.
	 */
	public function show( Request $request, Response $response, $id, Languages $languages ) {
		return $response->setContent( $languages->find( $id ) );
	}


	/**
	 * Retrieve select options for the search results.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param Languages $languages The Languages instance.
	 */
	public function options( Request $request, Response $response, Languages $languages ) {
		$result         = $this->index( $request, $response, $languages );
		$result['data'] = $languages->as_options( $result['data'] ?? [] );

		return $response->setContent( $result );
	}

	/**
	 * Index method
	 *
	 * This method is responsible for handling the index route. It retrieves the search, page, and limit parameters from the request object.
	 * If the search parameter is provided, it calls the search method on the Languages object with the specified page and limit parameters.
	 * Otherwise, it calls the all method on the Languages object with the specified page and limit parameters.
	 * The results are returned as an array.
	 *
	 * @param Request $request The request object containing the search, page, and limit parameters
	 * @param Response $response The response object for returning the results
	 * @param Languages $languages The Languages object for performing language-related operations
	 *
	 * @return array The array containing the search results or all languages
	 */
	public function index( Request $request, Response $response, Languages $languages ) {
		$search = $request->get( 'search', '' );
		$page   = $request->get( 'paged', 1 );
		$limit  = $request->get( 'limit', 50 );

		if ( $search ) {
			return $languages->search( $search );
		}

		return $languages->all( [
			'page'  => $page,
			'limit' => $limit,
		] );
	}
}
