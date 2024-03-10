<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\PhpOption\Option;
use CodeZone\Bible\Services\BibleBrains\Reference;
use CodeZone\Bible\Services\Options;
use function CodeZone\Bible\CodeZone\Router\container;
use function CodeZone\Bible\collect;

class Bibles extends Service {
	protected $endpoint = 'bibles';
	protected $default_options = [
		'limit' => 500,
	];


	/**
	 * Transforms a collection of records into an array of options.
	 *
	 * @param iterable $records The records to transform into options.
	 *
	 * @return array Returns an array of options.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
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
	 * Retrieves the books of a given code.
	 *
	 * @param string $code The code of the book.
	 * @param array $query An optional array of query parameters to filter the books.
	 *
	 * @return array Returns an array of books for the given code.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function books( $code, $query = [] ) {
		return $this->find( $code, $query )['data']['books'] ?? [];
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
			'value'    => $record['abbr'] ?? $record['id'],
			'itemText' => $record['name']
		];
	}

	/**
	 * Retrieves all records for a specific language.
	 *
	 * @param string $language_code The language code to filter the records by.
	 *
	 * @return array An array of records for the specified language.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function for_language( string $language_code, $query ) {

		$query = array_merge( $query, [
			'language_code' => $language_code
		] );

		return $this->all( $query );
	}

	/**
	 * Retrieves data for multiple languages.
	 *
	 * @param array $language_codes An array of language codes for which to retrieve data.
	 *
	 * @return array The data for the specified languages in the following format:
	 *               [
	 *                   'data' => [
	 *                       // Data for first language
	 *                       // Data for second language
	 *                       // ...
	 *                   ]
	 *               ]
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function for_languages( array $language_codes, array $query = [] ) {
		$result = [ 'data' => [] ];
		foreach ( $language_codes as $language_code ) {
			array_push( $result['data'], ...$this->for_language( $language_code, $query )['data'] );
		}

		return $result;
	}

	/**
	 * Retrieves the default types for a given language code.
	 *
	 * @param string $language_code The language code to retrieve the default types for.
	 *
	 * @return mixed The response from the API endpoint.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function default_for_language( string $language_code ) {
		return $this->get( $this->endpoint . '/defaults/types', [
			'language_code' => $language_code
		] );
	}

	/**
	 * Returns the default audio or video types for an array of language codes.
	 *
	 * @param array $language_codes An array of language codes.
	 *
	 * @return array An associative array containing the default audio or video types for each language code.
	 *               The array is structured as ['data' => [...]] where each element is the default audio type if available,
	 *               otherwise it is the default video type.
	 *
	 * @throws BibleBrainsException If an error occurs during the retrieval of default types for a language code.
	 */
	public function default_for_languages( array $language_codes ) {
		$result = [ 'data' => [] ];
		foreach ( $language_codes as $language_code ) {
			$types = $this->default_for_language( $language_code )->collect()->first();
			array_push( $result['data'], $types['audio'] ?? $types['video'] );
		}

		return $result;
	}

	public function content( $fileset, $book, $chapter, $verse_start, $verse_end ): array {
		return $this->get( 'filesets/' . $fileset . '/' . $book . '/' . $chapter, [
			'verse_start' => $verse_start,
			'verse_end'   => $verse_end
		] );
	}

	public function reference( $reference, $fileset ): array {
		[ $book, $chapter, $verse_start, $verse_end ] = Reference::spread( $reference );

		return $this->get( $this->endpoint . "/filesets/" . $fileset . "/" . $book . "/" . $chapter, [
			'verse_start' => $verse_start,
			'verse_end'   => $verse_end
		] );
	}
}
