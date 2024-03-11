<?php

namespace CodeZone\Bible\Services;

use CodeZone\Bible\Conditions\Plugin as PluginConditional;
use CodeZone\Bible\Gettext\Translation;
use CodeZone\Bible\Gettext\Translations as GettextTranslations;
use CodeZone\Bible\Illuminate\Support\Collection;
use function CodeZone\Bible\container;
use function CodeZone\Bible\get_plugin_option;
use function CodeZone\Bible\collect;

/**
 * Class Translations
 *
 * This class provides methods for translation-related operations.
 */
class Translations {
	protected $custom_translation_contexts = [
		'reader',
		'scripture'
	];


	public function __construct() {
		load_plugin_textdomain( 'bible-plugin', false, 'bible-plugin/languages' );
		add_filter( 'gettext_with_context', [ $this, 'gettext_with_context' ], 10, 4 );
	}

	/**
	 * Translates the given text using the GettextTranslations instance.
	 *
	 * @param string $translation The translation text.
	 * @param string $text The text to be translated.
	 * @param string $domain The translation domain.
	 *
	 * @return string The translated text.
	 */
	public function gettext_with_context( $translation, $text, $context, $domain ): string {
		if ( 'bible-plugin' === $domain && in_array( $context, $this->custom_translation_contexts ) ) {
			$custom_translation = $this->apply_custom_translation( $text );
			if ( $translation ) {
				return $custom_translation;
			}
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
	private function apply_custom_translation( $text ): string {
		return $this->custom_translations()->get( $text, '' );
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
		return collect( $this->get_text()->getTranslations() )->filter( function ( Translation $translation ) {
			return in_array( $translation->getContext(), $this->custom_translation_contexts );
		} )->map( function ( Translation $translation ) {
			return $translation->getOriginal();
		} )->unique()->values()->sort();
	}

	public function options(): array {
		return $this->strings()->map( function ( $string ) {
			return [
				'value'    => $string,
				'itemText' => $string
			];
		} )->toArray();
	}

	/**
	 * Retrieves the translations from the GettextTranslations instance.
	 */
	private function get_text(): GettextTranslations {
		return container()->make( GettextTranslations::class );
	}
}
