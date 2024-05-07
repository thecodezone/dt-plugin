<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;

/**
 * Index action for the controller.
 *
 * @param Request $request The HTTP request object.
 * @param Response $response The HTTP response object.
 * @param Bibles $bibles The Bibles service object.
 *
 * @return Response The HTTP response object.
 */
class BibleMediaTypesController {
	/**
	 * Index action for the controller.
	 *
	 * @param Request $request The HTTP request object.
	 * @param Response $response The HTTP response object.
	 * @param Bibles $bibles The Bibles service object.
	 *
	 * @return Response The HTTP response object.
	 */
	public function index( Request $request, Response $response, Bibles $bibles ) {
		return $response->setContent(
			$bibles->media_types()->json()
		);
	}

	/**
	 * Options action for the controller.
	 *
	 * @param Request $request The HTTP request object.
	 * @param Response $response The HTTP response object.
	 * @param Bibles $bibles The Bibles service object.
	 *
	 * @return Response The HTTP response object.
	 */
	public function options( Request $request, Response $response, Bibles $bibles ) {
		return $response->setContent(
			$bibles->media_type_options()
		);
	}
}
