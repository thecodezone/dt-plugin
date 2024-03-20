<?php

namespace CodeZone\Bible\Services\BibleBrains\Api;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\PhpOption\Option;
use CodeZone\Bible\Services\BibleBrains\Reference;
use CodeZone\Bible\Services\Options;
use function CodeZone\Bible\CodeZone\Router\container;
use function CodeZone\Bible\collect;

class Bibles extends ApiService {
	protected $endpoint = 'bibles';
	protected $default_options = [
		'limit' => 500,
	];

	/**
	 * Retrieves data for a specified code or language code. If no data is found, the default data for the specified language code is returned.
	 *
	 * @param string|null $code The code to search for.
	 * @param string|null $language_code The language code to search for.
	 * @param array $query Additional query parameters to filter the search.
	 *
	 * @return array An array containing the retrieved data. If data is found for the specified code, it is returned.
	 *               If no data is found, the default data for the specified language code is returned.
	 *               The array is structured based on the result of the search.
	 *               If the search is successful, the structure is ['data' => [...]] where 'data' contains the retrieved data.
	 *               If the search is unsuccessful and default data
	 * @throws BibleBrainsException
	 */
	public function find_or_default( string|null $code = null, string|int|null $language_id = null, array $query = [] ): array {
		if ( empty( $code ) && empty( $language_id ) ) {
			throw new BibleBrainsException( esc_html( 'Either a bible ID or a language ID must be provided.' ) );
		}


		if ( empty( $code ) && ! empty( $language_id ) ) {
			return $this->default_for_language( $language_id );
		}

		$result = $this->find( $code, $query );

		if ( empty( $result['data'] ) ) {
			$result = $this->default_for_language( $language_id );
		}

		return $result;
	}

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
	public function for_language( string $language_code, $query = [] ) {

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
	 * Returns the default audio or video type for a given language code.
	 *
	 * @param string $language_id The language code.
	 *
	 * @return array The default audio or video type for the language code.
	 *
	 * @throws BibleBrainsException If an error occurs during the retrieval of the default type for the language code.
	 */
	public function default_for_language( string $language_id ): array {
		$bible = $this->for_language( $language_id );

		return $this->find( $bible['data'][0]['abbr'] );
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
			$language = $this->default_for_language( $language_code );
			array_push( $result['data'], $language['data'] );
		}

		return $result;
	}

	/**
	 * Retrieves the content for a given fileset, book, chapter, verse range.
	 *
	 * @param string $fileset The fileset identifier.
	 * @param string $book The book identifier.
	 * @param int $chapter The chapter number.
	 * @param int $verse_start The starting verse number.
	 * @param int $verse_end The ending verse number.
	 *
	 * @return array An array containing the content for the given fileset, book, chapter, verse range.
	 *               The array structure is determined by the response of the API call.
	 *
	 * @throws BibleBrainsException If an error occurs during the retrieval of the content.
	 */
	public function content( $fileset, $book, $chapter, $verse_start, $verse_end ): array {
		if ( $fileset === "ENGESHP2DV" ) {
			dd( $this->endpoint . '/filesets/' . $fileset . '/' . $book . '/' . $chapter );
		}

		return $this->get( $this->endpoint . '/filesets/' . $fileset . '/' . $book . '/' . $chapter, [
			'verse_start' => $verse_start,
			'verse_end'   => $verse_end
		] );
	}

	/**
	 * Retrieves the verses of a given reference within a specified fileset.
	 *
	 * @param string $reference The reference string in the format "Book Chapter:VerseStart-VerseEnd".
	 * @param string $fileset The fileset identifier.
	 *
	 * @return array An associative array containing the verses for the given reference within the specified fileset.
	 *               The array is structured as ['data' => [...]] where each element represents a verse.
	 *
	 * @throws BibleBrainsException If an error occurs during the retrieval of verses for the given reference.
	 */
	public function reference( $reference, $fileset ): array {
		[ $book, $chapter, $verse_start, $verse_end ] = Reference::spread( $reference );

		return $this->get( $this->endpoint . "/filesets/" . $fileset . "/" . $book . "/" . $chapter, [
			'verse_start' => $verse_start,
			'verse_end'   => $verse_end
		] );
	}
}
