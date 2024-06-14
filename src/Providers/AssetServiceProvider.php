<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Services\Assets;
use function DT\Plugin\namespace_string;
use function DT\Plugin\route_url;

class AssetServiceProvider extends AbstractServiceProvider {

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
    public function provides(string $id): bool
    {
        return in_array($id, [
            Assets::class
        ]);
    }
}
