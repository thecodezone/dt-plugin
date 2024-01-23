<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\Illuminate\Http\Client\Factory;
use CodeZone\Bible\Services\BibleBrains\GuzzleMiddleware;
use function CodeZone\Bible\container;

class BibleBrainsServiceProvider extends ServiceProvider {
	/**
	 * Registers the middleware for the plugin.
	 *
	 * This method adds a filter to register middleware for the plugin.
	 * The middleware is added to the stack in the order it is defined above.
	 *
	 * @return void
	 */
	public function register(): void {
		Factory::macro( 'bibleBrains', function () {
			return $this->withMiddleware( container()->make( GuzzleMiddleware::class ) );
		} );
	}

	public function boot(): void {
		// TODO: Implement boot() method.
	}
}
