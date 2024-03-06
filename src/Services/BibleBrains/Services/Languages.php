<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Http\Client\Response;
use CodeZone\Bible\Illuminate\Support\Collection;
use CodeZone\Bible\Illuminate\Support\Str;
use function CodeZone\Bible\collect;

class Languages extends Service {
	protected $endpoint = 'languages';
	protected $default_options = [
		'include_translations' => false,
		'include_all_names'    => false,
		'limit'                => 500,
	];

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
			'value'         => (string) $record['id'],
			'language_code' => (string) $record['iso'],
			'itemText'      => (string) $record['name'],
		];
	}

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
			$records->unique( 'id' )
		);
	}
}
