<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\GuzzleHttp\Psr7\Uri;
use CodeZone\Bible\GuzzleHttp\Psr7\UriResolver;
use CodeZone\Bible\Psr\Http\Message\RequestInterface;

class GuzzleMiddleware {
	protected string $base_url = 'https://api.scripture.api.bible/v1';
	protected string $key;


	public function __construct() {
		$this->key = get_option( 'bible_plugin_bible_brains_key' );
	}


	public function __invoke( callable $handler ) {
		return function ( RequestInterface $request, array $options ) use ( $handler ) {
			$newUri = UriResolver::resolve( new Uri( $this->base_url ), $request->getUri() );

			// Add the 'key' query parameter
			$newUri = Uri::withQueryValue( $newUri, 'key', $this->key );

			// Update the request with the modified URI
			$request = $request->withUri( $newUri );

			// Call the next middleware handler
			return $handler( $request, $options );
		};
	}
}
