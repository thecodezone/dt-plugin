<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use function CodeZone\Bible\container;

/**
 * Class Reference
 *
 * This class provides methods for parsing and spreading book references.
 */
class Reference {
	/**
	 * Splits the given reference into individual components and returns them as an array.
	 *
	 * @param mixed $reference The reference to be split.
	 *
	 * @return array An array containing the book, chapter, verse start, and verse end components of the reference.
	 */
	public static function spread( $reference ): array {
		$reference = self::parse( $reference );

		return [
			$reference['book'],
			$reference['chapter'],
			$reference['verse_start'],
			$reference['verse_end'],
		];
	}

	/**
	 * Parses the given reference and returns an array.
	 *
	 * @param mixed $reference The reference to be parsed.
	 *
	 * @return array The parsed result as an array.
	 */
	public static function parse( mixed $reference ): array {
		if ( is_array( $reference ) ) {
			return self::parse_array( $reference );
		}

		return self::parse_string( $reference );
	}

	/**
	 * Normalize the given array by setting default values, trimming string values, and normalizing book value.
	 *
	 * @param array $array The array to be normalized.
	 *
	 * @return array The normalized array.
	 */
	public static function normalize_array( $array ) {
		$array = array_merge(
			[
				'book'        => '',
				'chapter'     => '',
				'verse_start' => '',
				'verse_end'   => '',
			],
			$array
		);

		$array = Arr::map( $array, function ( $value ) {
			if ( is_string( $value ) ) {
				return trim( $value );
			}

			return $value;
		} );

		if ( isset( $array['verse'] ) && ! $array['verse_start'] ) {
			$array['verse_start'] = $array['verse'];
			$array['verse_end']   = $array['verse'];
		}

		$books         = container()->make( Books::class );
		$array['book'] = $books->normalize( $array['book'] );

		return $array;
	}

	/**
	 * Parse the given array by calling the normalize_array method.
	 *
	 * @param array $references The array to be parsed.
	 *
	 * @return array The parsed array.
	 */
	private static function parse_array( array $references ) {
		return static::normalize_array( $references );
	}

	/**
	 * Parse the given string reference and return the normalized array representation.
	 *
	 * @param string $reference The string reference to be parsed.
	 *
	 * @return array The normalized array representation of the parsed string reference.
	 */
	private static function parse_string( string $reference ) {
		$reference = self::normalize( $reference );

		[ $book, $chapter_and_verse ] = array_pad( explode( ' ', $reference ?? "", 2 ), 2, null );
		[ $chapter, $verses ] = array_pad( explode( ':', $chapter_and_verse ?? "", 2 ), 2, null );
		$verses = array_pad( explode( '-', $verses ?? "", 2 ), 2, null );
		if ( ! $verses[1] ) {
			$verses[1] = $verses[0];
		}

		$parsed = [
			'book'        => $book,
			'chapter'     => $chapter,
			'verse_start' => $verses[0],
			'verse_end'   => $verses[1],
		];

		return self::normalize_array( $parsed );
	}

	/**
	 * Normalize the given string reference by replacing double spaces with single space,
	 * replacing '–' with '-', removing leading and trailing white spaces, and replacing '—' with '-'.
	 *
	 * @param string $reference The string reference to be normalized.
	 *
	 * @return string The normalized string reference.
	 */
	private static function normalize( string $reference ) {
		$reference = Str::replace( '  ', ' ', $reference );
		$reference = Str::replace( '–', '-', $reference );
		$reference = trim( $reference );

		return Str::replace( '—', '-', $reference );
	}
}
