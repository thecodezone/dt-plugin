<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
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
	 */
	public function search( array $parameters = [] ): array {
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
		$parameters = array_merge( $parameters, $this->reference->parse( $parameters ) );

		// If we already have a fileset, no need to query for anything else
		if ( $parameters['fileset'] ) {
			return $this->by_fileset( $parameters['fileset'], $parameters );
		}

		//If no language, fetch the default from options
		if ( ! $parameters['language'] ) {
			$parameters['language'] = $this->options->get( 'language' );
		}

		//Fetch the media type meta
		$media_type = $this->media_types->find( $parameters['media_type'] );
		if ( ! $media_type ) {
			throw new BibleBrainsException( esc_attr( "Invalid media type: {$parameters['media_type']}" ) );
		}
		$fileset_types = $media_type['fileset_types'];

		//Fetch the bible or default to the default bible for the language
		$bible               = $parameters['bible']
			? $this->bibles->find( $parameters['bible'] )
			: $this->bibles->default_for_language( $parameters['language'] );
		$parameters['bible'] = $bible['id'];

		//Pluck the book from the bible data
		$book = Arr::first( $bible->books, fn( $book ) => $book['book_id'] === $parameters['book'] );
		if ( ! $book ) {
			throw new BibleBrainsException( esc_attr( "Bible does not contain book: {$parameters['book']}" ) );
		}

		//Pluck the fileset that matches our fileset type and book testament
		$fileset = Arr::first( $book['filesets'], function ( $fileset ) use ( $fileset_types, $book ) {
			return in_array( $fileset['type'], $fileset_types )
			       && Str::contains( $fileset["size"], $book["testament"] );
		} );

		if ( ! $fileset && $fileset !== "text" ) {
			throw new BibleBrainsException( esc_attr( "Bible, {$parameters["bible"]}, does not contain {$parameters["media_type"]} fileset for {$parameters['book']}." ) );
		}

		return $this->by_fileset( $fileset['id'], $parameters );
	}

	/**
	 * Retrieves the content of a scripture reference using the search method.
	 *
	 * @param string $reference The scripture reference to search for
	 * @param array $parameters Additional parameters for the search method
	 *
	 * @return array The search results for the given scripture reference and parameters
	 */
	public function reference( string $reference = "", array $parameters = [] ): array {
		$reference  = $this->reference->parse( $reference );
		$parameters = array_merge( $reference, $parameters );

		return $this->search( $parameters );
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
