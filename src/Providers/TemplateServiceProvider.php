<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\League\Plates\Engine;
use CodeZone\Bible\Services\Plates\Escape;
use function CodeZone\Bible\views_path;
use function CodeZone\Bible\namespace_string;

/**
 * Register the plates view engine
 * @see https://platesphp.com/
 */
class TemplateServiceProvider extends ServiceProvider {
	/**
	 * Register the view engine singleton and any extensions
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Engine::class, function ( $container ) {
			return new Engine( views_path() );
		} );
		$this->container->make( Engine::class )->loadExtension(
			$this->container->make( Escape::class )
		);

		add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
			$allowed_css[] = 'bible-plugin';

			return $allowed_css;
		} );

		add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
			$allowed_js[] = 'bible-plugin';

			return $allowed_js;
		} );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function boot(): void {
	}
}
