<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;

class FileSets {
	/**
	 * Returns the group based on the given type.
	 *
	 * @param array $bible The array containing the bible data.
	 * @param string $type The type to check.
	 *
	 * @return string The group name. Returns "dpb-vid" if the type contains the word "video", otherwise returns "dpb-prod".
	 */
	public function group_from_type( array $bible, string $type ): string {
		return Str::contains( $type, 'video' ) ? "dpb-vid" : "dbp-prod";
	}

	/**
	 * Plucks the first matching fileset based on the given conditions.
	 *
	 * @param array $bible The array containing the bible data.
	 * @param array $book The array containing the book data.
	 * @param array $fileset_types The array containing the fileset types to search for.
	 *
	 * @throws BibleBrainsException Throws an exception if no fileset matching the conditions is found.
	 */
	public function pluck( array $bible, array $book, array $fileset_types ): array {
		$fileset_group = $this->group_from_type( $bible, $fileset_types[0] );

		$fileset = Arr::first( $bible['filesets'][ $fileset_group ] ?? [], function ( $fileset ) use ( $fileset_types, $book ) {
			return in_array( $fileset['type'], $fileset_types )
			       && $fileset["size"] === "C" || Str::contains( $fileset["size"], $book["testament"] );
		}, null );

		if ( ! $fileset ) {
			throw new BibleBrainsException( "Fileset not found" );
		}

		return $fileset;
	}
}
