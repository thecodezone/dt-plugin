<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use CodeZone\Bible\Services\Options;
use CodeZone\Bible\Services\Translations;
use WhiteCube\Lingua\Service as Lingua;

/**
 * Class Language
 *
 * Represents a language and provides methods for resolving the language code,
 * finding supported languages, and resolving the default language.
 *
 * @package YourPackage
 */
class Language {

	/**
	 * Constructor.
	 *
	 * @param Options $options The options object.
	 * @param Languages $languages The languages object.
	 * @param Translations $translations The translations object.
	 */
	public function __construct( private Options $options, Languages $languages, private Translations $translations ) {
	}

	/**
	 * Get the resolved locale from the translations object.
	 *
	 * @return string The resolved locale.
	 */
	public function locale() {
		return $this->translations->resolve_locale();
	}

	/**
	 * Get the ISO 639-3 code for the current locale.
	 *
	 * @return string The ISO 639-3 code.
	 */
	public function iso() {
		return Lingua::create( $this->locale() )->toISO_639_3();
	}

	/**
	 * Resolves the language based on the ISO code.
	 *
	 * @return Language|null The resolved language object or null if not found.
	 */
	public function resolve() {
		$iso = $this->iso();

		if ( $this->supported( $iso ) ) {
			return $this->find_or_default( $iso );
		}

		return $this->default();
	}

	/**
	 * Finds a language based on the given code or resolves it if not found.
	 *
	 * @param string $code The code of the language.
	 *
	 * @return Language|null The found language or null if not found.
	 */
	public function find_or_resolve( $code ) {
		$language = $this->find( $code );
		if ( ! $language ) {
			$language = $this->resolve();
		};

		return $language;
	}

	/**
	 * Checks if a language is supported based on the given code.
	 *
	 * @param string $code The code of the language.
	 *
	 * @return bool Whether the language is supported or not.
	 */
	public function supported( $code ) {
		try {
			$languages = $this->options->get( 'languages', null, true );
		} catch ( \Exception $e ) {
			return false;
		}
		if ( ! is_array( $languages ) ) {
			return false;
		}

		return (bool) Arr::first( $languages, function ( $config ) use ( $code ) {
			return $config['value'] === $code;
		} );
	}

	/**
	 * Gets the default language from the options or returns the default value if it's not configured.
	 *
	 * @return Language|null The default language or null if not found.
	 */
	public function default() {
		$language = $this->options->get( 'languages', null, true );
		if ( ! is_array( $language ) ) {
			return $this->options->get_default( 'languages' );
		}
		$default_language = Arr::first( $language, function ( $config ) {
			return $config['is_default'] ?? false;
		} );
		if ( ! $default_language ) {
			$default_language = Arr::first( $language );
		}

		return $default_language;
	}

	/**
	 * Find a language by its code.
	 *
	 * @param string $code The code of the language to find.
	 *
	 * @return array|false The language configuration if found, false otherwise.
	 */
	public function find( $code ) {
		if ( ! $code ) {
			return false;
		}
		try {
			$languages = $this->options->get( 'languages', null, true );
			if ( ! is_array( $languages ) ) {
				return false;
			}

			return Arr::first( $languages, function ( $config ) use ( $code ) {
				return $config['value'] === $code;
			} );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Find a language by its code or return the default language.
	 *
	 * @param string $code The code of the language to find.
	 *
	 * @return array The language configuration if found, otherwise the default language configuration.
	 */
	public function find_or_default( $code ) {
		$language = $this->find( $code );
		if ( ! $language ) {
			$language = $this->default();
		}

		return $language;
	}
}
