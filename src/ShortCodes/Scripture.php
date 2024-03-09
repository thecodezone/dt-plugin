<?php

namespace CodeZone\Bible\ShortCodes;

use function CodeZone\Bible\template;

/**
 * Class Scripture
 *
 * The Scripture class is responsible for rendering the [tbp_scripture] shortcode.
 */
class Scripture {
	/**
	 * The __construct method is the constructor of a class. It is used to initialize an object when it is created.
	 * In this case, the constructor registers a shortcode in WordPress using the `add_shortcode` function.
	 * The registered shortcode is 'tbp_scripture' and it is mapped to the `render` method of the current object.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'tbp_scripture', [ $this, 'render' ] );
	}

	/**
	 * Render function for the shortcode.
	 *
	 * @param array $atts An array of attributes passed to the shortcode.
	 *                    The following attributes are available:
	 *                    - book: The book name. Default is an empty string.
	 *                    - chapter: The chapter number. Default is an empty string.
	 *                    - verse: The verse number. Default is an empty string.
	 *                    - media: The media source. Default is an empty string.
	 *
	 * @return void This method does not return a value.
	 */
	public function render( $atts ) {
		$atts = shortcode_atts( [
			'book'    => '',
			'chapter' => '',
			'verse'   => '',
			'media'   => ''
		], $atts );


		echo template( 'shortcodes/scripture', $atts );
	}
}
