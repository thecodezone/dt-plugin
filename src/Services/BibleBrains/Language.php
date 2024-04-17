<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use CodeZone\Bible\Services\Options;
use WhiteCube\Lingua\Service as Lingua;

class Language {

	public function __construct( private Options $options, Languages $languages ) {}

	public function locale() {
		return get_locale();
	}

	public function iso() {
		return Lingua::create( $this->locale() )->toISO_639_3();
	}

	public function resolve() {
		$iso = $this->iso();

		if ( $this->supported( $iso ) ) {
			return $this->find_or_default( $iso );
		}

		return $this->default();
	}

	public function find_or_resolve( $code ) {
		$language = $this->find( $code );
		if ( !$language ) {
			$language = $this->resolve();
		}
		return $language;
	}

	public function supported( $code ) {
		try {
			$languages = $this->options->get( 'languages', false, true );
		} catch ( \Exception $e ) {
			return false;
		}
		if ( !is_array( $languages ) ) {
			return false;
		}
		return (bool) Arr::first( $languages, function ( $config ) use ( $code ) {
			return $config['value'] === $code;
		});
	}

	public function default() {
		$language = $this->options->get( 'languages', false, true );
		if ( !is_array( $language ) ) {
			return $this->options->get_default( 'languages' );
		}
		$default_language = Arr::first( $language, function ( $config ) {
			return $config['is_default'] ?? false;
		} );
		if ( !$default_language ) {
			$default_language = Arr::first( $language );
		}
		return $default_language;
	}

	public function find( $code ) {
		try {
			$languages = $this->options->get( 'languages', false, true );
			if ( !is_array( $languages ) ) {
				return false;
			}
			return Arr::first( $languages, function ( $config ) use ( $code ) {
				return $config['value'] === $code;
			});
		} catch ( \Exception $e ) {
			return false;
		}
	}

	public function find_or_default( $code ) {
		$language = $this->find( $code );
		if ( !$language ) {
			$language = $this->default();
		}
		return $language;
	}
}
