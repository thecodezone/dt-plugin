<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use CodeZone\Bible\Services\Options;
use function CodeZone\Bible\validate;

/**
 * The Scripture class is responsible for handling scripture references and retrieving scripture content from a Bible object.
 *
 * @package YourPackage
 */
class Scripture {
	/**
	 * Constructor method for the class.
	 *
	 * @param Bibles $bibles The instance of the Bibles class.
	 * @param Reference $reference The instance of the Reference class.
	 * @param Options $options The instance of the Options class.
	 * @param MediaTypes $media_types The instance of the MediaTypes class.
	 */
	public function __construct(
		private Bibles $bibles,
		private Books $books,
		private FileSets $file_sets,
		private Languages $languages,
		private Reference $reference,
		private Options $options,
		private MediaTypes $media_types
	) {
	}

	/**
	 * Search for verses in the Bible using various parameters.
	 *
	 * @param array $parameters An associative array of search parameters.
	 *                          - language: The language to search in. Defaults to null.
	 *                          - fileset: The fileset to search in. Defaults to null.
	 *                          - bible: The specific Bible to search in. Defaults to null.
	 *                          - book: The specific book of the Bible to search in. Defaults to null.
	 *                          - chapter: The specific chapter of the Bible to search in. Defaults to null.
	 *                          - media_type: The media type to search for. Defaults to 'text'.
	 *                          - verse_start: The starting verse to search from. Defaults to null.
	 *                          - verse_end: The ending verse to search to. Defaults to null.
	 *
	 * @return array The search results as*@throws BibleBrainsException If an invalid media type is specified.
	 * @throws BibleBrainsException
	 */
	public function search( array $parameters = [] ): array {
		$parameters = $this->normalize_query( $parameters );
		$fileset    = $this->query_fileset_id( $parameters );

		return $this->by_fileset( $fileset, $parameters );
	}

	/**
	 * Normalize the search query parameters by merging them with default values.
	 *
	 * @param array $parameters An associative array of search parameters.
	 *                          - language: The language to search in. Defaults to null.
	 *                          - fileset: The fileset to search in. Defaults to null.
	 *                          - bible: The specific Bible to search in. Defaults to null.
	 *                          - book: The specific book of the Bible to search in. Defaults to*/
	private function normalize_query( $parameters ): array {
		$parameters = array_merge( [
			'language'    => null,
			'fileset'     => null,
			'bible'       => null,
			'book'        => null,
			'chapter'     => null,
			'media_type'  => 'text',
			'verse_start' => null,
			'verse_end'   => null,
		], $parameters );

		return array_merge( $parameters, $this->reference->parse( $parameters ) );
	}

	/**
	 * Determine the appropriate fileset to search in based on the given parameters.
	 *
	 * @param array $parameters An associative array of search parameters.
	 *                          - language (string): The language to search in. Defaults to null.
	 *                          - fileset (string): The fileset to search in. Defaults to null.
	 *                          - bible (string): The specific Bible to search in. Defaults to null.
	 *                          - book (string): The specific book of the Bible to search in. Defaults to null.
	 *                          - chapter (string): The specific chapter of the Bible to search in. Defaults to null.
	 *                          - media_type (string): The media type to search for. Defaults to 'text'.
	 *                          - verse_start (string): The starting verse to search from. Defaults to null.
	 *                          - verse_end (string): The ending verse to search to. Defaults to null.
	 *
	 * @return array The fileset as an array.
	 *
	 * @throws BibleBrainsException If an invalid media type is specified or if there are any other errors.
	 */
	private function query_fileset( array $parameters ): array {
		$parameters = $this->normalize_query( $parameters );

		//If no language, fetch the default from options
		if ( ! $parameters['language'] ) {
			$parameters['language'] = $this->options->get( 'language', false, true );
		}
		$media_type    = $this->media_types->find( $parameters['media_type'] );
		$fileset_types = $media_type['fileset_types'];
		$language      = $this->languages->find( $parameters['language'] )["data"];
		$bible         = $this->bibles->find_or_default( $parameters['bible'], $language['id'] )["data"];
		$book          = $this->books->pluck( $parameters['book'], $bible['books'] );
		$fileset       = $this->file_sets->pluck( $bible, $book, $fileset_types );

		if ( ! $fileset ) {
			throw new BibleBrainsException( esc_attr( "Bible, {$bible['name']}, does not contain {$parameters["media_type"]} fileset for {$book['book']}." ) );
		}

		return $fileset;
	}

	/**
	 * Retrieve the fileset ID based on the provided parameters.
	 *
	 * @param array $parameters An associative array of parameters.
	 *                          - language: The language to search in. Defaults to null.
	 *                          - fileset: The fileset to search in. Defaults to null.
	 *                          - bible: The specific Bible to search in. Defaults to null.
	 *                          - book: The specific book of the Bible to search in. Defaults to null.
	 *                          - chapter: The specific chapter of the Bible to search in. Defaults to null.
	 *                          - media_type: The media type to search for. Defaults to 'text'.
	 *                          - verse_start: The starting verse to search from. Defaults to null.
	 *                          - verse_end: The ending verse to search to. Defaults to null.
	 *
	 * @return string The fileset ID.
	 *
	 * @throws BibleBrainsException If an invalid media type is specified.
	 *
	 * @see query_fileset()
	 */
	private function query_fileset_id( array $parameters ) {
		if ( $parameters['fileset'] ) {
			return $parameters['fileset'];
		}

		return $this->query_fileset( $parameters )['id'];
	}

	/**
	 * Search for verses in the Bible using the given reference and additional parameters.
	 *
	 * @param string $reference The reference to search for in the Bible.
	 * @param array $parameters An associative array of additional search parameters.
	 *                          - language: The language to search in. Defaults to null.
	 *                          - fileset: The fileset to search in. Defaults to null.
	 *                          - bible: The specific Bible to search in. Defaults to null.
	 *                          - book: The specific book of the Bible to search in. Defaults to null.
	 *                          - chapter: The specific chapter of the Bible to search in. Defaults to null.
	 *                          - media_type: The media type to search for. Defaults to 'text'.
	 *                          - verse_start: The starting verse to search from. Defaults to null.
	 *                          - verse_end: The ending verse to search to. Defaults to null.
	 *
	 * @return array The search results as an associative array.
	 * @throws BibleBrainsException If an invalid media type is specified during the search.
	 */
	public function by_reference( string $reference = "", array $parameters = [] ): array {
		$reference  = $this->reference->parse( $reference );
		$parameters = $this->normalize_query( array_merge( $reference, $parameters ) );
		$fileset    = $this->query_fileset_id( $parameters );

		return $this->by_fileset( $fileset, $parameters );
	}

	/**
	 * Search for verses in the Bible using a specific fileset and additional parameters.
	 *
	 * @param string $fileset The fileset to search in.
	 * @param array $parameters An associative array of search parameters.
	 *                          - language: The language to search in. Defaults to null.
	 *                          - bible: The specific Bible to search in. Defaults to null.
	 *                          - book: The specific book of the Bible to search in. Defaults to null.
	 *                          - chapter: The specific chapter of the Bible to search in. Defaults to null.
	 *                          - verse_start: The starting verse to search from. Defaults to null.
	 *                          - verse_end: The ending verse to search to. Defaults to null.
	 *
	 * @return array The search results.
	 * @throws BibleBrainsException If an invalid media type is specified.
	 */
	public function by_fileset( string $fileset, array $parameters = [] ): array {
		[ $book, $chapter, $verse_start, $verse_end ] = $this->reference->spread( $parameters );

		return $this->bibles->content( $fileset, $book, $chapter, $verse_start, $verse_end );
	}
}
