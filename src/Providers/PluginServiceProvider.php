<?php

namespace DT\Plugin\Providers;

use DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Plugin\League\Container\Exception\NotFoundException;
use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Plugin;
use DT\Plugin\Psr\Container\ContainerExceptionInterface;
use DT\Plugin\CodeZone\WPSupport\Rewrites\RewritesInterface;

/**
 * Class PluginServiceProvider
 *
 * This class is the main service provider for the plugin.
 */
class PluginServiceProvider extends AbstractServiceProvider {
    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return in_array( $id, [
            Plugin::class
        ]);
    }


    /**
     * Register the plugin and its service providers.
     *
     * @return void
     * @throws NotFoundException|ContainerExceptionInterface
     */
    public function register(): void {
        $this->getContainer()->addShared( Plugin::class, function () {
            return new Plugin(
                $this->getContainer(),
                $this->getContainer()->get( RewritesInterface::class ),
                $this->getContainer()->get( ConfigInterface::class )
            );
        } );
    }
}
