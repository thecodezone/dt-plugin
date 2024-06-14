<?php

/**
 * @var $config DT\Plugin\League\Config\Configuration
 */

use DT\Plugin\MagicLinks\ExampleMagicLink;

$config->merge( [
    'magic' => [
        'links' => [
            ExampleMagicLink::class
        ]
    ]
] );
