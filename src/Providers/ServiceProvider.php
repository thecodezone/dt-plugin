<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\Illuminate\Container\Container;

/**
 * Class ServiceProvider
 *
 * This class is an abstract base class for service providers.
 * Service providers are responsible for setting up and booting application services.
 */
abstract class ServiceProvider {
	protected $container;

	/**
	 * ServiceProvider constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Do any setup needed before the theme is ready.
	 */
	abstract public function register(): void;

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	abstract public function boot(): void;
}
