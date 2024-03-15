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

		return $this->query( $parameters );
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
;

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
	private function query( array $parameters ): array {
		$parameters = $this->normalize_query( $parameters );

		//If no language, fetch the default from options
		if ( ! $parameters['language'] ) {
			$parameters['language'] = $this->options->get( 'language', false, true );
		}
		$media_type = $this->media_types->find( $parameters['media_type'] );

		$fileset_types = $media_type['fileset_types'];
		$language      = $this->languages->find( $parameters['language'] )["data"];
		$bible         = $this->bibles->find_or_default( $parameters['bible'], $language['id'] )["data"];

		$book = $this->books->pluck( $parameters['book'], $bible['books'] );

		if ( ! $book ) {
			throw new BibleBrainsException( esc_attr( "Bible, {$bible['name']}, does not contain {$parameters['book']}." ) );
		}

		$fileset = $this->file_sets->pluck( $bible, $book, $fileset_types );

		if ( ! $fileset ) {
			throw new BibleBrainsException( esc_attr( "Bible, {$bible['name']}, does not contain {$parameters["media_type"]} fileset for {$book['book']}." ) );
		}
		$parameters['fileset'] = $fileset['id'];

		$content = $this->fetch_content( $parameters );

		return [
			...$parameters,
			'media_type' => $media_type,
			'language'   => $language,
			'bible'      => $bible,
			'book'       => $book,
			'fileset'    => $fileset,
			'content'    => $content['data'] ?? []
		];
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
	 *                          - verse_start: The starting verbiblese to search from. Defaults to null.
	 *                          - verse_end: The ending verse to search to. Defaults to null.
	 *
	 * @return array The search results as an associative array.
	 * @throws BibleBrainsException If an invalid media type is specified during the search.
	 */
	public function by_reference( string $reference = "", array $parameters = [] ): array {
		$reference  = $this->reference->parse( $reference );
		$parameters = $this->normalize_query( array_merge( $reference, $parameters ) );

		return $this->query( $parameters );
	}

	/**
	 * Fetches the content of Bible verses based on the specified parameters.
	 *
	 * @param array $parameters An associative array of parameters specifying the content to fetch.
	 *                          - fileset: The fileset from which to fetch the content.
	 *                          - book: The book of the Bible from which to fetch the content.
	 *                          - chapter: The chapter of the Bible from which to fetch the content.
	 *                          - verse_start: The starting verse of the content to fetch.
	 *                          - verse_end: The ending verse of the content to fetch.
	 *
	 * @return array An array containing the fetched content.
	 * @throws BibleBrainsException If an error occurs while fetching the content.
	 */
	private function fetch_content( array $parameters = [] ): array {

		return $this->bibles->content(
			$parameters['fileset'],
			$parameters['book'],
			$parameters['chapter'],
			$parameters['verse_start'],
			$parameters["verse_end"]
		);
	}
}
