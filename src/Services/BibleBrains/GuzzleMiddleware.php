<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\GuzzleHttp\Psr7\Uri;
use CodeZone\Bible\GuzzleHttp\Psr7\UriResolver;
use CodeZone\Bible\Psr\Http\Message\RequestInterface;
use function CodeZone\Bible\get_plugin_option;

class GuzzleMiddleware {
	protected string $base_url = 'https://4.dbt.io/api/';
	protected string $key;


	public function __construct() {
		$this->key = get_plugin_option( 'bible_brains_key', defined( 'TBP_BIBLE_BRAINS_KEY' ) ? TBP_BIBLE_BRAINS_KEY : '', true );
	}


	public function __invoke( callable $handler ) {
		return function ( RequestInterface $request, array $options ) use ( $handler ) {
			$newUri = UriResolver::resolve( new Uri( $this->base_url ), $request->getUri() );

			parse_str( $newUri->getQuery(), $query );

			// Add the 'key' query parameter
			if ( empty( $query['key'] ) ) {
				$newUri = Uri::withQueryValue( $newUri, 'key', $this->key );
			}
			if ( empty( $query['v'] ) ) {
				$newUri = Uri::withQueryValue( $newUri, 'v', '4' );
			}

			// Update the request with the modified URI
			$request = $request->withUri( $newUri );

			// Call the next middleware handler
			return $handler( $request, $options );
		};
	}
}
