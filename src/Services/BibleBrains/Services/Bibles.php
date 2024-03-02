<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use function CodeZone\Bible\collect;

class Bibles extends Service {
	protected $endpoint = 'bibles';
	protected $default_options = [
		'limit' => 500,
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
			'value' => $record['abbr'] ?? $record['id'],
			'label' => $record['name'] ?? Arr::get( $record, 'translations.0.name' ),
		];
	}

	public function for_language( string $language_code ) {
		return $this->all( [
			'language_code' => $language_code
		] );
	}

	public function for_languages( array $language_codes ) {
		$result = [ 'data' => [] ];
		foreach ( $language_codes as $language_code ) {
			array_push( $result['data'], ...$this->for_language( $language_code )['data'] );
		}

		return $result;
	}

	public function default_for_language( string $language_code ) {
		return $this->http->get( $this->endpoint . '/defaults/types', [
			'language_code' => $language_code
		] );
	}

	public function default_for_languages( array $language_codes ) {
		$result = [ 'data' => [] ];
		foreach ( $language_codes as $language_code ) {
			$types = $this->default_for_language( $language_code )->collect()->first();
			array_push( $result['data'], $types['audio'] ?? $types['video'] );
		}

		return $result;
	}

	public function media_types() {
		return $this->http->get( $this->endpoint . '/filesets/media/types' );
	}

	public function media_type_options() {
		return [
			'data' => $this->media_types()
			               ->collect()
                        ->map( function ( $label, $value ) {
				               return [
					               'value' => $value,
					               'label' => $label
				               ];
                        } )->filter( function ( $option ) {
                            $whitelist = [ "audio_drama", "audio", "video_stream" ];

                            return in_array( $option['value'], $whitelist );
						} )->push( [
                            'value' => 'text',
                            'label' => 'Text'
                        ] )->sortBy( 'value' )->values()
		];
	}
}
