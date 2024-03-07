<?php

namespace CodeZone\Bible\Services;

use CodeZone\Bible\Conditions\Plugin as PluginConditional;
use CodeZone\Bible\Gettext\Translations as GettextTranslations;
use CodeZone\Bible\Illuminate\Support\Collection;
use function CodeZone\Bible\container;
use function CodeZone\Bible\get_plugin_option;

/**
 * Class Translations
 *
 * This class provides methods for translation-related operations.
 */
class Translations {
	public function __construct() {
		add_filter( 'gettext', [ $this, 'gettext_filter' ], 10, 3 );
		add_filter( 'gettext_with_context', [ $this, 'gettext_with_context_filter' ], 10, 3 );
	}

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
	 * Translates the given text using the GettextTranslations instance.
	 *
	 * @param string $translation The translation text.
	 * @param string $text The text to be translated.
	 * @param string $domain The translation domain.
	 *
	 * @return string The translated text.
	 */
	public function gettext_filter( $translation, $text, $domain ): string {
		if ( 'bible-plugin' === $domain ) {
			$translation = $this->translate( $text );
		}

		return $translation;
	}

	/**
	 * Applies a translation context filter to the given translation.
	 *
	 * @param string|null $translation The original translation.
	 * @param string $text The text to be translated.
	 * @param string $context The translation context.
	 * @param string $domain The text domain for the translation.
	 *
	 * @return string|null The filtered translation with the applied context, or the original translation if the text domain is not 'bible-plugin'.
	 */
	public function gettext_with_context_filter( $translation, $text, $context, $domain ): ?string {
		if ( 'bible-plugin' === $domain ) {
			$translation = $this->translate( $text, $context );
		}

		return $translation;
	}

	/**
	 * Translates the given text using the 'bible_plugin' translation domain.
	 *
	 * @param string $text The text to be translated.
	 *
	 * @return string The translated text.
	 */
	public function translate( $text, $context = [] ): string {
		if ( count( $context ) ) {
			// phpcs:ignore
			$default = _x( $text, $context, 'bible-plugin' );
		} else {
			// phpcs:ignore
			$default = __( $text, 'bible-plugin' );
		}

		if ( ! $default ) {
			$default = $text;
		}

		return $this->custom_translations()->get( $text, $default );
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
		return collect( get_plugin_option( 'translations', [] ) );
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
	private function get_text(): GettextTranslations {
		return container()->make( GettextTranslations::class )->getTranslations();
	}
}
