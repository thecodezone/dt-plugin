<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;

/**
 * Class BibleController
 *
 * The BibleController class is responsible for handling requests related to Bible operations.
 *
 * @package YourNamespace\YourPackageName
 */
class BibleController {
	/**
	 * Retrieve a bible.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param Bibles $bibles The Bibles instance.
	 */
	public function show( Request $request, Response $response, $id, Bibles $bibles ) {
		return $response->setContent( $bibles->find( $id ) );
	}

	/**
	 * Retrieve select options for the search results.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param Bibles $bibles The Bibles instance.
	 */
	public function options( Request $request, Response $response, Bibles $bibles ) {
		$result         = $this->index( $request, $response, $bibles );
		$result['data'] = $bibles->as_options( $result['data'] ?? [] );

		return $response->setContent( $result );
	}

	/**
	 * Index method
	 *
	 * This method is responsible for handling the index route. It retrieves the search, page, and limit parameters from the request object.
	 * If the search parameter is provided, it calls the search method on the Bibles object with the specified page and limit parameters.
	 * Otherwise, it calls the all method on the Bibles object with the specified page and limit parameters.
	 * The results are returned as an array.
	 *
	 * @param Request $request The request object containing the search, page, and limit parameters
	 * @param Response $response The response object for returning the results
	 * @param Bibles $bibles The Bibles object for performing bible-related operations
	 *
	 * @return array The array containing the search results or all bibles
	 */
	public function index( Request $request, Response $response, Bibles $bibles ) {
		$search        = $request->get( 'search', '' );
		$language_code = $request->get( 'language_code', '' );
		$page          = $request->get( 'paged', 1 );
		$limit         = $request->get( 'limit', 25 );

		if ( $search ) {
			return $bibles->search( $search, [
				'page'          => $page,
				'limit'         => $limit,
				'language_code' => $language_code,
			] )->collect()->toArray();
		}

		return $bibles->all( [
			'page'  => $page,
			'limit' => $limit,
		] )->json();
	}
}
