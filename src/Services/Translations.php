<?php

namespace CodeZone\Bible\Services;

use CodeZone\Bible\Gettext\Translations as GettextTranslations;
use CodeZone\Bible\Illuminate\Support\Collection;
use function CodeZone\Bible\container;


/**
 * Class Translations
 *
 * This class provides methods for translation-related operations.
 */
class Translations {
	/**
	 * Array of keywords that should be blacklisted for translation.
	 *
	 * @var string[]
	 */
	protected $blacklist_keywords = [
		'github',
		'plugin'
	];

	/**
	 * Translates the given text using the 'bible_plugin' translation domain.
	 *
	 * @param string $text The text to be translated.
	 *
	 * @return string The translated text.
	 */
	public function translate( $text ) {
		// phpcs:ignore
		return get_option( 'bible_plugin_translations', __( $text, 'bible_plugin' ) );
	}

	/**
	 * Retrieves the custom translations stored in the 'bible_plugin_translations' option.
	 *
	 * This method returns a Collection object that contains all the custom translations
	 * stored in the 'bible_plugin_translations' option. If the option is not set, an empty
	 * array is returned.
	 *
	 * @return Collection A Collection object containing the custom translations.
	 */
	public function custom_translations(): Collection {
		return collect( get_option( 'bible_plugin_translations', [] ) );
	}

	/**
	 * Retrieves the strings that are available for translation
	 * except the ones that are blacklisted.
	 *
	 * @return Collection A collection of filtered strings.
	 */
	public function strings(): Collection {
		return collect( $this->get_text()->getTranslations() )->keys()->filter( function ( $key ) {
			return ! in_array( $key, $this->blacklist_keywords );
		} )->map( function ( $key ) {
			return preg_replace( '/[[:^print:]]/', '', $key );
		} );
	}

	/**
	 * Retrieves the translations from the GettextTranslations instance.
	 *
	 * @return GettextTranslations The GettextTranslations instance containing the translations.
	 */
	public function get_text(): GettextTranslations {
		return container()->make( GettextTranslations::class )->getTranslations();
	}
}
