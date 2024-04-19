<?php

namespace CodeZone\Bible\ShortCodes;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Services\Assets;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use CodeZone\Bible\Services\BibleBrains\FileSets;
use CodeZone\Bible\Services\BibleBrains\MediaTypes;
use CodeZone\Bible\Services\BibleBrains\Scripture as ScriptureService;
use function CodeZone\Bible\container;
use function CodeZone\Bible\view;
use function CodeZone\Bible\cast_bool_values;

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
	public function __construct( private ScriptureService $scripture, private Assets $assets, private MediaTypes $media_types ) {
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

		if ( ! $attributes ) {
			$attributes = [];
		}

		$attributes = shortcode_atts( [
			'language'     => '',
			'reference'    => '',
			'media'        => 'text',
			'heading'      => true,
			"heading_text" => "",
			"bible"        => "",
		], cast_bool_values( $attributes ) );

		$error = false;
		$result = [];

		if ( ! $this->media_types->exists( $attributes['media'] ) ) {
			$error = _x( "Invalid media type", "shortcode", "bible-plugin" );
		} else {
			try {
				$result = $this->scripture->by_reference( $attributes['reference'], [
					'language'   => $attributes['language'],
					'bible'      => $attributes['bible'],
				] ) ?? [];
			} catch ( \Exception $e ) {
				$error  = $e->getMessage();
			}
		}

		if ( ! $error
		     && (
				 empty( $result['media'] )
		        || empty( $result['media'][$attributes['media']] )
		        || empty( $result['media'][$attributes['media']]['content'] )
		        || empty( $result['media'][$attributes['media']]['fileset'] )
		     )
		) {
			$error = _x( "No results found", "shortcode", "bible-plugin" );
		}

		$media = $result['media'][$attributes['media']] ?? [];
		$content = $media['content']['data'] ?? [];
		$fileset = $media['fileset'] ?? [];

		return view( 'shortcodes/scripture', [
			'error'        => $error,
			'fileset_type' => $fileset['type'] ?? "",
			'attributes'   => $attributes,
			'content'      => $content,
			"reference"    => [
				"verse_start" => $result["verse_start"] ?? "",
				"verse_end"   => $result["verse_end"] ?? "",
				"chapter"     => $result["chapter"] ?? "",
				"book"        => $result["book"]["name"] ?? "",
			]
		] );
	}
}