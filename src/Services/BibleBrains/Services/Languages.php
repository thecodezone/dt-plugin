<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Http\Client\Response;
use CodeZone\Bible\Illuminate\Support\Collection;
use CodeZone\Bible\Illuminate\Support\Str;
use function CodeZone\Bible\collect;

class Languages extends Service {
	/**
	 * Retrieves languages as options for a dropdown select field.
	 *
	 * @param iterable $languages The languages to process.
	 *
	 * @return array The languages as options, with 'value' and 'label' keys.
	 */
	public function as_options( iterable $records ): array {
		$records = collect( $records );
		return parent::as_options(
			$records->filter( function ( $language ) {
				return ! Str::contains( $language['name'], [ '(', ')' ] )
				       && ! Str::contains( $language['autonym'], [ '(', ')' ] );
			} )->values()
		);
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

		$params = array_merge( [
			'include_translations' => false,
			'include_all_names'    => false
		], $params );

		return $this->http->get( 'languages/search/' . $name, $params );
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
		$params = array_merge( [
			'include_translations' => false,
			'include_all_names'    => false,
			'limit'                => 500,
		], $params );

		return $this->http->get( 'languages', $params );
	}
}
