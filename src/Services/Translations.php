<?php

namespace CodeZone\Bible\Services;

use CodeZone\Bible\Gettext\Loader\PoLoader;
use CodeZone\Bible\Gettext\Translation;
use CodeZone\Bible\Gettext\Translations as GettextTranslations;
use CodeZone\Bible\Illuminate\Support\Collection;
use CodeZone\Bible\Illuminate\Support\Str;
use WhiteCube\Lingua\Service;
use function CodeZone\Bible\container;
use function CodeZone\Bible\get_plugin_option;
use function CodeZone\Bible\languages_path;
use function CodeZone\Bible\collect;

/**
 * Class Translations
 *
 * This class provides methods for translation-related operations.
 */
class Translations {
	/**
	 * @var array $custom_translation_contexts The array of custom translation contexts
	 *
	 * This array stores the custom translation contexts.
	 * @see https://developer.wordpress.org/reference/functions/gettext_with_context/
	 */
	protected $custom_translation_contexts = [
		'reader',
		'scripture',
		'shortcode'
	];


	/**
	 * Constructs a new instance of the class.
	 *
	 * Loads the plugin text domain for "bible-plugin" to enable translation support.
	 * Registers the "gettext_with_context" and "plugin_locale" filters.
	 */
	public function __construct() {
		load_plugin_textdomain( 'bible-plugin', false, 'bible-plugin/languages' );
		add_filter( 'gettext_with_context', [ $this, 'gettext_with_context' ], 10, 4 );
		add_filter( "plugin_locale", [ $this, 'plugin_locale' ], 10, 2 );
	}

	/**
	 * Retrieves a collection of paths to language files.
	 *
	 * @return Collection The collection of language file paths.
	 */
	public function paths(): Collection {
		return collect( glob( languages_path( '*.po' ) ) );
	}

	/**
	 * Retrieves a collection of files.
	 *
	 * @return Collection The collection of files.
	 */
	public function files(): Collection {
		return $this->paths()->map( function ( $file ) {
			return ( new PoLoader() )->loadFile( $file );
		} );
	}

	/**
	 * Retrieves the languages from the files in the collection.
	 *
	 * @return Collection A collection of languages.
	 */
	public function languages(): Collection {
		return $this->files()->map( function ( $file ) {
			return Str::lower( $file->getLanguage() );
		} )->push( 'en-us' )
		            ->push( 'en' );
	}

	/**
	 * Retrieves the browser languages from the `Accept-Language` header in the HTTP request.
	 *
	 * @return Collection List of browser languages in descending order of priority.
	 */
	public function browser_languages(): Collection {
		//phpcs:ignore
		$language_string = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

		if ( empty( $language_string ) ) {
			return collect();
		}

		$lang_parse = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

		$languages = [];
		// Loop through each item of the array and split it into a separate array where index 0 is the language and index 1 is the priority
		foreach ( $lang_parse as $lang ) {
			$lang_parts                  = explode( ';', $lang );
			$languages[ $lang_parts[0] ] = isset( $lang_parts[1] ) ? str_replace( 'q=', '', $lang_parts[1] ) : 1;
		}

		// Sort the languages by priority
		arsort( $languages );

		return collect( array_keys( $languages ) );
	}

	/**
	 * Resolves the locale based on the browser language and supported languages.
	 * If the browser language is supported, the converted language will be used.
	 * Otherwise, the site's locale will be used.
	 *
	 * @return string The resolved locale.
	 */
	public function resolve_locale() {
		$browser_languages = $this->browser_languages();

		if ( ! $browser_languages || ! count( $browser_languages ) ) {
			return get_locale();
		}

		$supported_languages = $this->languages();

		// Convert the browser language using Lingua Service

		foreach ( $browser_languages as $browser_lang ) {
			$converted_lang = Service::createFromW3C( $browser_lang )->toISO_639_1();

			// If browser language is supported in plugin, use that locale
			if ( $supported_languages->contains( Str::lower( $converted_lang ) ) || $supported_languages->contains( Str::lower( $browser_lang ) ) ) {
				return $converted_lang;
			}
		}

		// If browser language isn't set or isn't supported in plugin, use the site's locale
		return get_locale();
	}


	/**
	 * Filters the plugin locale.
	 *
	 * This method is used to set the locale of the plugin to the one set in the WordPress
	 * settings. This is necessary because the plugin uses the 'bible-plugin' text domain
	 * for translations, and the locale of the plugin must be set to the one set in the
	 * WordPress settings in order for the translations to work correctly.
	 *
	 * @param string $locale The locale of the plugin.
	 *
	 * @return string The locale of the plugin.
	 */
	public function plugin_locale( $locale, $domain ): string {
		if ( $domain === 'bible-plugin' ) {
			return $this->resolve_locale();
		}

		return $locale;
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
			if ( $custom_translation ) {
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

	/**
	 * Retrieves an array of options.
	 *
	 * @return array An array of options with each option having a 'value' and 'itemText' property.
	 */
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
