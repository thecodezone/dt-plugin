<?php

/**
 * @var $config DT\Plugin\League\Config\Configuration
 */

$config->merge( [
    'routes' => [
        'files' => [
            [
                "file" => "api.php",
                'rewrite' => 'dt/plugin/api',
                'query' => 'dt-plugin-api',
            ],
            [
                "file" => "web.php",
                'rewrite' => 'dt/plugin',
                'query' => 'dt-plugin',
            ]
        ]
    ],
    'middleware' => [
        // CustomMiddleware::class,
    ],
] );
