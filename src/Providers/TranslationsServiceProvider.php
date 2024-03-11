<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\Gettext\Loader\PoLoader;
use CodeZone\Bible\Gettext\Translations as GetText;
use CodeZone\Bible\Services\Translations;
use function CodeZone\Bible\languages_path;

class TranslationsServiceProvider extends ServiceProvider {

	public function register(): void {
		$this->container->singleton( GetText::class, function ( $app ) {
			return $app->make( PoLoader::class )->loadFile( languages_path( 'bible-plugin-es_MX.po' ) );
		} );

		$this->container->singleton( Translations::class, function () {
			return new Translations();
		} );

		$this->container->make( Translations::class );
	}

	public function boot(): void {
		load_plugin_textdomain( 'bible-plugin', false, 'bible-plugin/languages' );
	}
}
