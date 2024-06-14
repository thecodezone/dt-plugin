<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Services\Assets;
use function DT\Plugin\namespace_string;
use function DT\Plugin\route_url;

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
            $allowed_css[] = 'dt-plugin';
            return $allowed_css;
        } );

        add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
            $allowed_js[] = 'dt-plugin';
            return $allowed_js;
        } );

        add_filter( namespace_string( 'javascript_globals' ), function ( $data ) {
            return array_merge($data, [
                'nonce'        => wp_create_nonce( 'dt-plugin' ),
                'urls'         => [
                    'root'           => esc_url_raw( trailingslashit( route_url() ) ),
                ],
                'translations' => [
                    'Disciple Tools' => __( 'Disciple Tools', 'dt-plugin' ),
                ]
            ]);
        });

        $this->getContainer()->add( 'assets', function () {
            return new Assets();
        } );
    }
}
