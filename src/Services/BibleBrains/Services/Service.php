<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Http\Client\Factory as Http;
use CodeZone\Bible\Illuminate\Http\Client\PendingRequest;
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
	public function map_option( $record ): array {
		return [
			'value' => $record['id'],
			'label' => $record['name'],
		];
	}
}
