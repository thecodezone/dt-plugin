<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\CodeZone\WPSupport\Options\Options;
use DT\Plugin\CodeZone\WPSupport\Options\OptionsInterface;
use function DT\Plugin\config;

/**
 * Class OptionsServiceProvider
 *
 * This class is a service provider responsible for registering the Options class
 * into the container and providing the default options for the application.
 *
 * @package YourPackage
 */
class OptionsServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Provide the services that this provider is responsible for.
	 *
	 * @param string $id The ID to check.
	 * @return bool Returns true if the given ID is provided, false otherwise.
	 */
	public function provides( string $id ): bool
	{
		return in_array( $id, [
			OptionsInterface::class,
		] );
	}


	/**
	 * Registers the Options class into the container.
	 *
	 * @return void
	 */
	public function register(): void
	{
		$this->container->add( OptionsInterface::class, function () {
			return new Options(
				config()->get( 'options.defaults' ),
				config()->get( 'options.prefix' )
			);
		} );
	}

	public function boot(): void
	{
	}
}
