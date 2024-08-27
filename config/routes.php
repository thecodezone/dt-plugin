<?php

/**
 * @var $config DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Plugin\routes_path;

$config->merge( [
	'routes' => [
		'rewrites' => [
			'^dt/plugin/api/?$' => 'index.php?dt-plugin-api=/',
			'^dt/plugin/api/(.+)/?' => 'index.php?dt-plugin-api=$matches[1]',
			'^dt/plugin/?$' => 'index.php?dt-plugin=/',
			'^dt/plugin/(.+)/?' => 'index.php?dt-plugin=$matches[1]',
		],
		'files' => [
			'api' => [
				"file" => "api.php",
				'query' => 'dt-plugin-api',
				'path' => 'dt/plugin/api',
			],
			'web' => [
				"file" => "web.php",
				'query' => 'dt-plugin',
				'path' => 'dt/plugin',
			]
		],
		'middleware' => [
			// CustomMiddleware::class,
		],
	],
] );
