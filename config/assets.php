<?php

/**
 * @var $config DT\Plugin\League\Config\Configuration
 */

use DT\Plugin\Nette\Schema\Expect;
use function DT\Plugin\config;
use function DT\Plugin\plugin_path;
use function DT\Plugin\route_url;

$config->merge( [
    'assets' => [
        'allowed_styles' => [
            'dt-plugin',
            'dt-plugin-admin',
        ],
        'allowed_scripts' =>[
            'dt-plugin',
            'dt-plugin-admin',
        ],
        'javascript_global_scope' => '$dt_plugin',
        'javascript_globals' => [],
        'manifest_dir' => plugin_path( '/dist' )
    ]
] );



add_action('wp_loaded', function () use ( $config ) {
    $config->set( 'assets.javascript_globals',[
        'nonce' => wp_create_nonce( config( 'plugin.nonce_name' ) ),
        'urls' => [
            'root' => esc_url_raw( trailingslashit( route_url() ) ),
        ]
    ]);
});
