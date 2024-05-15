<?php

namespace CodeZone\Bible\ShortCodes;

use CodeZone\Bible\Services\Assets;
use function CodeZone\Bible\view;
use function CodeZone\Bible\cast_bool_values;
use function CodeZone\Bible\request;

/**
 * Class Bible
 *
 * This class represents a Bible shortcode handler.
 */
class Bible {
	/**
	 * The assets service.
	 *
	 * @var Assets
	 */
	protected $assets;
	
	/**
	 * Constructs a new instance of the class.
	 *
	 * Adds a shortcode callback to handle the 'tbp-bible' shortcode.
	 *
	 * @return void
	 */
	public function __construct( Assets $assets ) {
		$this->assets = $assets;

		add_shortcode( 'tbp-bible', [ $this, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueues the scripts and styles for the shortcode.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! has_shortcode( get_the_content(), 'tbp-bible' ) ) {
			return;
		}

		$this->assets->wp_enqueue_scripts();
	}

	/**
	 * Renders the Bible shortcode.
	 *
	 * @param array $attributes The attributes for the Bible shortcode.
	 *
	 * @return string The rendered Bible shortcode view.
	 */
	public function render( $attributes ) {
		if ( ! $attributes ) {
			$attributes = [];
		}

		$attributes = shortcode_atts( [
			'reference' => 'John 1',
		], cast_bool_values( $attributes ) );

		if ( request()->has( 'reference' ) ) {
			$attributes['reference'] = request()->get( 'reference' );
		}


		return view( 'shortcodes/bible', [
			'attributes' => $attributes
		] );
	}
}
