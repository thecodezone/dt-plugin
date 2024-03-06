<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\ShortCodes\Bible;
use CodeZone\Bible\ShortCodes\Scripture;

class ShortcodeProvider extends ServiceProvider {

	protected $shortcodes = [
		Bible::class,
		Scripture::class
	];

	public function register(): void {
		collect( $this->shortcodes )->each( function ( $shortcode ) {
			$this->container->make( $shortcode );
		} );
	}

	public function boot(): void {
	}
}
