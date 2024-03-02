<?php

namespace CodeZone\Bible\Middleware;

use CodeZone\Bible\CodeZone\Router\Middleware\Middleware;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Symfony\Component\HttpFoundation\Response;
use Exception;
use function CodeZone\Bible\container;

class BibleBrains implements Middleware {
	public function handle( Request $request, Response $response, $next ): Response {
		$bibles = container()->make( Bibles::class );
		try {
			$result = $bibles->find( 'ENGESV' );
			if ( $result->getStatusCode() !== 401 ) {
				return $next( $request, $response );
			}
		} catch ( Exception $e ) {
			//If the request fails, we will redirect to the settings page
		}

		if ( is_admin() ) {
			$response->isRedirect( admin_url( 'page=bible-plugin&tab=bible_brains_key' ) );
		} else {
			$response->setStatusCode( 404 );
		}


		return $response;
	}
}
