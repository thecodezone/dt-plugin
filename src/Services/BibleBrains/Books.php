<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;

/**
 * Class Books
 */
class Books {
	public function __construct( protected Bibles $bible ) {
	}

	/**
	 * Retrieves all books from the Bible using the specified translation.
	 *
	 * @return array The list of books in the Bible.
	 */
	public function all() {
		return $this->bible->books( 'ENGESV' );
	}

	/**
	 * Find the first occurrence of a book in the given array.
	 *
	 * @param string $book The book to search for.
	 *
	 * @return mixed The first occurrence of the book, or null if not found.
	 */
	public function find( $book ) {
		return Arr::first( $this->all(), function ( $b ) use ( $book ) {
			return $b['book_id'] === Str::upper( $book )
			       || $b['name'] === Str::ucfirst( $book )
			       || $b['name_short'] === Str::ucfirst( $book );
		} );
	}

	/**
	 * Normalize a book by returning its book_id if found in the data array, otherwise return the original book value
	 *
	 * @param string $book The book to be normalized.
	 *
	 * @return string The normalized book_id if found in the data array, otherwise the original book value.
	 */
	public function normalize( $book ) {
		$data = $this->find( $book );

		if ( ! $data ) {
			return $book;
		}

		return Arr::get( $data, 'book_id', $book );
	}
}
