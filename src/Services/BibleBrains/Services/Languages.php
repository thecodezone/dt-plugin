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
				if ( ! isset( $language['name'] ) || ! isset( $language['autonym'] ) ) {
					throw new \Exception( 'Attempting to create options for invalid language.' );
				}

				return ! Str::contains( $language['name'], [ '(', ')' ] )
				       && ! Str::contains( $language['autonym'], [ '(', ')' ] );
			} )->values()
		);
	}
}
