<?php

/**
 * @var $config DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Plugin\MagicLinks\ExampleMagicLink;

$config->merge( [
	'magic' => [
		'links' => [
			ExampleMagicLink::class
		]
	]
] );
