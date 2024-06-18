<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Services\AssetQueue;
use DT\Plugin\Services\AssetQueueInterface;
use DT\Plugin\Services\Assets;
use function DT\Plugin\config;
use function DT\Plugin\namespace_string;

/**
 * Class AssetServiceProvider
 *
 * The AssetServiceProvider class provides asset-related services.
 */
class AssetServiceProvider extends AbstractServiceProvider {

    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise. */
    public function provides( string $id ): bool
    {
        return in_array($id, [
            AssetQueue::class,
            Assets::class
        ]);
    }

    /**
     * Register method.
     *
     * This method is used to register filters and dependencies for the plugin.
     *
     * @return void
     */
    public function register(): void{
        add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
            return array_merge( $allowed_css, config( 'assets.allowed_styles' ) );
        } );

        add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
            return array_merge( $allowed_js, config( 'assets.allowed_scripts' ) );
        } );

        add_filter( namespace_string( 'javascript_globals' ), function ( $data ) {
            return array_merge( $data, config( 'assets.javascript_globals' ) );
        });

        $this->getContainer()->add( AssetQueueInterface::class, function () {
            return new AssetQueue();
        } );

        $this->getContainer()->add( Assets::class, function () {
            return new Assets(
                $this->getContainer()->get( AssetQueueInterface::class )
            );
        } );
    }
}
