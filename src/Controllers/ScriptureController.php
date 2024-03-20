<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use CodeZone\Bible\Services\BibleBrains\Scripture;
use function CodeZone\Bible\validate;

class ScriptureController {

	public function index( Request $request, Response $response, Scripture $scripture ) {
		$errors = validate( $request->all(), [
			'reference' => 'required|string'
		] );

		if ( $errors ) {
			return $response->setStatusCode( 400 )->setContent( $errors );
		}

		try {
			$result = $scripture->by_reference( $request->get( 'reference' ) );
		} catch ( \Exception $e ) {
			return $response->setStatusCode( $e->getCode() )->setContent( $e->getMessage() );
		}

		return $response->setContent( $result );
	}
}
