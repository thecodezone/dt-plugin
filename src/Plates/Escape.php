<?php

namespace DT\Plugin\Plates;

use DT\Plugin\League\Plates\Engine;
use DT\Plugin\League\Plates\Extension\ExtensionInterface;

class Escape implements ExtensionInterface {


	public function register( Engine $engine ) {
		$engine->registerFunction( 'esc_html_e', 'esc_html_e' );
		$engine->registerFunction( 'esc_html', 'esc_html' );
		$engine->registerFunction( 'esc_attr_e', 'esc_attr_e' );
		$engine->registerFunction( 'esc_attr', 'esc_attr' );
		$engine->registerFunction( 'esc_url', 'esc_url' );
	}
}