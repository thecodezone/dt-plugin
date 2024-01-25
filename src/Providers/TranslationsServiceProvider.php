<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\Gettext\Loader\PoLoader;
use CodeZone\Bible\Gettext\Translations;
use function CodeZone\Bible\languages_path;

class TranslationsServiceProvider extends ServiceProvider {

	public function register(): void {
		$this->container->singleton( Translations::class, function ( $app ) {
			return $app->make( PoLoader::class )->loadFile( languages_path( 'es_MX.po' ) );
		} );
	}

	public function boot(): void {
		load_plugin_textdomain( 'bible-plugin', false, 'bible-plugin/languages' );
	}
}
