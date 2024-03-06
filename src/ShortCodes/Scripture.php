<?php

namespace CodeZone\Bible\ShortCodes;

use function CodeZone\Bible\template;

class Scripture {
	public function __construct() {
		add_shortcode( 'tbp_scripture', [ $this, 'render' ] );
	}

	public function render( $atts ) {
		$atts = shortcode_atts( [
			'book'    => '',
			'chapter' => '',
			'verse'   => '',
		], $atts );

		echo template( 'shortcodes/scripture', $atts );
	}
}
