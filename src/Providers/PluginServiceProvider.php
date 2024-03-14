<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\Illuminate\Filesystem\Filesystem;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Translation\FileLoader;
use CodeZone\Bible\Illuminate\Translation\Translator;
use CodeZone\Bible\Illuminate\Validation\Factory;
use CodeZone\Bible\Services\Translations;

class PluginServiceProvider extends ServiceProvider {
	/**
	 * List of providers to register
	 *
	 * @var array
	 */
	protected $providers = [
		TranslationsServiceProvider::class,
		BibleBrainsServiceProvider::class,
		ShortcodeProvider::class,
		ViewServiceProvider::class,
		ConditionsServiceProvider::class,
		MiddlewareServiceProvider::class,
		AdminServiceProvider::class,
		RouterServiceProvider::class,
	];

	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
		$this->container->singleton( Request::class, function () {
			return Request::capture();
		} );

		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->register();
		}

		$this->container->bind( FileLoader::class, function ( $container ) {
			return new FileLoader( $container->make( Filesystem::class ), 'lang' );
		} );

		$this->container->bind( Factory::class, function ( $container ) {
			$loader     = $container->make( FileLoader::class );
			$translator = new Translator( $loader, 'en' );

			return new Factory( $translator, $container );
		} );
	}


	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->boot();
		}
	}
}
