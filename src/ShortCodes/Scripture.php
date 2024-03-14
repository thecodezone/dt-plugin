<?php

namespace CodeZone\Bible\ShortCodes;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Services\Assets;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use CodeZone\Bible\Services\BibleBrains\FileSets;
use CodeZone\Bible\Services\BibleBrains\Scripture as ScriptureService;
use function CodeZone\Bible\container;
use function CodeZone\Bible\view;

/**
 * Class Scripture
 *
 * This class represents a shortcode for rendering scripture in a WordPress site.
 * It registers the shortcode 'tbp_scripture' and maps it to the `render` method of the current object.
 * The `render` method processes the shortcode attributes, retrieves the scripture data, and renders the output.
 *
 * @package YourPackage
 */
class Scripture {
	/**
	 * The __construct method is the constructor of a class. It is used to initialize an object when it is created.
	 * In this case, the constructor registers a shortcode in WordPress using the `add_shortcode` function.
	 * The registered shortcode is 'tbp_scripture' and it is mapped to the `render` method of the current object.
	 *
	 * @return void
	 */
	public function __construct( private ScriptureService $scripture, private Assets $assets, private Bibles $bibles ) {
		add_shortcode( 'tbp-scripture', [ $this, 'render' ] );
	}

	/**
	 * Renders a scripture shortcode.
	 *
	 * @param array $attributes The attributes for the shortcode.
	 *  - language (optional) The language of the scripture.
	 *  - reference (optional) The reference of the scripture.
	 *  - media_type (optional) The media type of the scripture.
	 *
	 * @throws BibleBrainsException
	 */
	public function render( $attributes ) {
		$this->assets->enqueue();

		$attributes = shortcode_atts( [
			'language'  => '',
			'reference' => '',
			'media'     => 'text'
		], $attributes );

		$error = false;
		try {
			$result = $this->scripture->by_reference( $attributes['reference'], [
				'language'   => $attributes['language'],
				'media_type' => $attributes['media'],
			] ) ?? [];

		} catch ( \Exception $e ) {
			$error  = $e->getMessage();
			$result = false;
		}


		if ( ! $error && empty( $result["content"] ) ) {
			$error = _x( "No results found", "shortcode", "bible-plugin" );
		}

		//dd( $result['content'] );

		return view( 'shortcodes/scripture', [
			'error'        => $error,
			'fileset_type' => $result["fileset"]["type"] ?? '',
			'attributes'   => $attributes,
			'content'      => $result['content'] ?? [],
		] );
	}
}
