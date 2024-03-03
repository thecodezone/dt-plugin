<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Http\Client\Factory as Http;
use CodeZone\Bible\Illuminate\Http\Client\PendingRequest;
use CodeZone\Bible\Illuminate\Http\Client\Response;
use CodeZone\Bible\Illuminate\Support\Collection;
use CodeZone\Bible\Illuminate\Support\Str;
use function CodeZone\Bible\collect;

abstract class Service {
	/**
	 * @var Http
	 */
	protected PendingRequest $http;

	/**
	 * @param Http $http
	 */
	public function __construct( Http $http ) {
		$this->http = $http->bibleBrains();
	}

	public function get( $endpoint, $params = [] ) {
		$cache_key = 'bible-plugin-' . $endpoint . '?' . http_build_query( $params );
		$cached    = get_transient( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$result = $this->http->get( $endpoint, $params );

		if ( $result->successful() ) {
			set_transient( $cache_key, $result->json(), 60 * 60 * 24 * 7 );
		} else {
			throw new \Exception( $result->json()['error']['message'] );
		}

		return $result->json();
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
			try {
				$result = $this->get( $this->endpoint . '/' . $id );
				if ( ! empty( $result['data'] ) ) {
					$data[] = $result['data'];
				}
			} catch ( \Exception $e ) {
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
	 */
	public function find( array|string|int $id ) {
		if ( is_array( $id ) ) {
			return $this->find_many( $id );
		}

		return $this->get( $this->endpoint . '/' . $id );
	}
}
