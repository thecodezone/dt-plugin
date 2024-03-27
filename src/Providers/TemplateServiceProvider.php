<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Plates\Engine;
use DT\Plugin\Services\Plates\Escape;
use function DT\Plugin\namespace_string;
use function DT\Plugin\views_path;

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
			$allowed_css[] = 'dt-plugin';

			return $allowed_css;
		} );

		add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
			$allowed_js[] = 'dt-plugin';

			return $allowed_js;
		} );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function boot(): void {
	}
}
