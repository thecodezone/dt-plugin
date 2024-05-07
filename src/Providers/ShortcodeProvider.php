<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\ShortCodes\Bible;
use CodeZone\Bible\ShortCodes\Scripture;

/**
 * Class ShortcodeProvider
 *
 * @package Your\Namespace
 */
class ShortcodeProvider extends ServiceProvider {

	protected $shortcodes = [
		Bible::class,
		Scripture::class
	];

	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
		collect( $this->shortcodes )->each( function ( $shortcode ) {
			$this->container->make( $shortcode );
		} );
	}

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
	}
}
