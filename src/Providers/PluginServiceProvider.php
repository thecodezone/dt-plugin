<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Plugin;

class PluginServiceProvider extends AbstractServiceProvider {
    /**
     * List of providers to register
     *
     * @var array
     */
    protected $providers = [
        PostTypeServiceProvider::class,
        MagicLinkServiceProvider::class,
        TemplateServiceProvider::class,
        RouteServiceProvider::class,
        AdminServiceProvider::class
    ];


    /**
     * Register the plugin and its service providers.
     *
     * @return void
     */
    public function register(): void {
        $this->getContainer()->addShared( Plugin::class, function () {
            return new Plugin( $this->getContainer() );
        } );

        foreach ( $this->providers as $provider ) {
			$this->getContainer()->addServiceProvider( $this->getContainer()->get( $provider ) );
		}
    }

    public function provides( string $id ): bool
    {
        return in_array($id, [
            Plugin::class
        ]);
    }
}
