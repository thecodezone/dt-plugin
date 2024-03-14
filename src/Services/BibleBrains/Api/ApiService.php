<?php

namespace CodeZone\Bible\Services\BibleBrains\Api;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Http\Client\Factory as Http;
use CodeZone\Bible\Illuminate\Http\Client\PendingRequest;
use CodeZone\Bible\Illuminate\Http\Client\Response;
use CodeZone\Bible\Illuminate\Support\Collection;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\Cache;
use Exception;
use function CodeZone\Bible\collect;
use function CodeZone\Bible\container;

abstract class ApiService {
	/**
	 * @var Http
	 */
	protected PendingRequest $http;

	/**
	 * @param Http $http
	 */
	public function __construct( Http $http ) {
		$this->init( $http );
	}

	public function init( Http $http = null ) {
		$http       = $http ?? container()->make( Http::class );
		$this->http = $http->bibleBrains();
	}

	/**
	 * Sends a GET request to the specified endpoint with optional parameters.
	 *
	 * @param string $endpoint The URL endpoint to send the GET request to.
	 * @param array $params Additional parameters for the GET request (optional).
	 *                     Available options:
	 *                     - cache: Whether to cache the response (default: true).
	 *
	 * @return array The JSON response from the GET request.
	 * @throws BibleBrainsException If the GET request is unsuccessful and returns an error.
	 */
	public function get( $endpoint, $params = [] ) {
		$cache        = container()->make( Cache::class );
		$cache_key    = $endpoint . '?' . http_build_query( $params );
		$should_cache = $params['cache'] ?? true;
		$cached       = $should_cache ? $cache->get( $cache_key ) : false;
		if ( $cached ) {
			return $cached;
		}

		$response = $this->http->get( $endpoint, $params );
		$result   = $response->json();

		if ( $response->successful() ) {
			$cache->set( $cache_key, $result );
		} else {
			if ( ! isset( $result['error'] ) ) {
				// phpcs:ignore
				throw new BibleBrainsException( 'An error occurred while retrieving data from the BibleBrains API.' );
			}
			// phpcs:ignore
			throw new BibleBrainsException( $result['error']['message'] ?? $result['error'] );
		}

		return $result;
	}

	/**
	 * Convert records to options array.
	 *
	 * @param iterable $records Collection of records.
	 *
	 * @return array The options array.
	 */
	public function as_options( iterable $records ): array {
		$records = collect( $records );

		return array_values( $records->map( function ( $record ) {
			return $this->map_option( $record );
            } )->filter( function ( $option ) {
			return ! empty( $option['value'] )
			&& ! empty( $option['itemText'] );
		} )->toArray() );
	}

	/**
	 * Maps an option record to an associative array.
	 *
	 * @param array $record The option record to map.
	 *
	 * @return array The mapped option as an associative array, where the 'value' key corresponds to the ID in the record,
	 *               and the 'label' key corresponds to the name in the record.
	 */
	public function map_option( array $record ): array {
		return [
			'value'    => (string) $record['id'],
			'itemText' => (string) $record['name'],
		];
	}

	/**
	 * Searches for languages based on the provided name.
	 *
	 * @param string $name The name to search for.
	 * @param array $params Additional parameters for the search (optional).
	 *                    Available options:
	 *                    - include_translations: Whether to include translations of the language names (default: false).
	 *                    - include_all_names: Whether to include all names of the languages (default: false).
	 *
	 * @return Response The search results.
	 * @throws BibleBrainsException If the search request is unsuccessful and returns an error.
	 */
	public function search( $name, $params = [] ) {

		$params = array_merge( $this->default_options, $params );

		return $this->get( $this->endpoint . '/search/' . $name, $params );
	}

	/**
	 * Retrieves all pages of data based on the provided parameters.
	 *
	 * @param array $params Additional parameters for retrieving data (optional).
	 *                     Available options:
	 *                     - page: The page number to retrieve (default: 1).
	 *
	 * @return Response The combined data from all pages.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 *
	 */
	public function all_pages( $params = [] ) {
		$page        = 0;
		$response    = $this->all( $params );
		$data        = collect( $response->collect()->get( 'data' ) );
		$total_pages = $response->collect()->get( 'meta' )['pagination']['total_pages'] ?? 0;
		while ( $total_pages > $page ) {
			$page++;
			$data->push( ...$this->all( array_merge( $params, [ 'page' => $page ] ) )->collect()->get( 'data' ) );
		}

		return $data;
	}

	/**
	 * Retrieves all languages.
	 *
	 * @param array $params Additional parameters for retrieving the languages (optional).
	 *                    Available options:
	 *                    - include_translations: Whether to include translations of the language names (default: false).
	 *                    - include_all_names: Whether to include all names of the languages (default: false).
	 *                    - limit: The maximum number of languages to retrieve (default: 500).
	 *
	 * @return Response The retrieved languages.
	 * @throws BibleBrainsException Ifdd(  the request is unsuccessful and returns an error.
	 */
	public function all( $params = [] ) {
		$params = array_merge( $this->default_options, $params );

		return $this->get( $this->endpoint, $params );
	}

	/**
	 * Retrieves multiple items based on the provided IDs.
	 *
	 * @param array $ids An array containing the IDs of the items to retrieve.
	 *
	 * @return mixed The retrieved items. If successful, it will be an array of data for each item.
	 *               Otherwise, it will be null.
	 */
	public function find_many( array $ids ) {
		$data   = [];
		$result = [ 'data' => [] ];
		foreach ( $ids as $id ) {
			if ( ! $id ) {
				continue;
			}
			try {
				$result = $this->get( $this->endpoint . '/' . $id );
				if ( ! empty( $result['data'] ) ) {
					$data[] = $result['data'];
				}
				// phpcs:ignore
			} catch ( BibleBrainsException $e ) {
				// Ignore errors and continue.
			}
			$result['data'] = $data;
		}

		return $result;
	}

	/**
	 * Finds a resource based on the provided identifier.
	 *
	 * @param array|string|int $id The identifier of the resource.
	 *
	 * @return Response The resource found.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function find( array|string|int $id, $params = [] ) {
		if ( is_array( $id ) ) {
			return $this->find_many( $id );
		}

		return $this->get( $this->endpoint . '/' . $id, $params );
	}
}
