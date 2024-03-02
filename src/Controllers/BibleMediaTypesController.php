<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;

class BibleMediaTypesController {
	public function index( Request $request, Response $response, Bibles $bibles ) {
		return $response->setContent(
			$bibles->media_types()->json()
		);
	}

	public function options( Request $request, Response $response, Bibles $bibles ) {
		return $response->setContent(
			$bibles->media_type_options()
		);
	}
}
