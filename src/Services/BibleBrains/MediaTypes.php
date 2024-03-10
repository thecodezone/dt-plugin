<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use function CodeZone\Bible\collect;

/**
 * MediaTypes class represents a collection of media types and their properties.
 */
class MediaTypes {
	/**
	 * Media types and their properties.
	 *
	 * @var array $media_types
	 * Key-value pairs where the key is the media type and the value is an array of properties.
	 * Properties include the label, fileset types, and group.
	 */
	private $media_types = [
		'audio_drama'  => [
			'label'         => 'Dramatized Audio',
			'fileset_types' => [ 'audio_drama' ],
			'group'         => 'dbp-prod'
		],
		'audio'        => [
			'label'         => 'Audio',
			'fileset_types' => [ 'audio' ],
			'group'         => 'dbp-prod'
		],
		'video_stream' => [
			'label'         => 'Video',
			'fileset_types' => [ 'video_stream' ],
			'group'         => 'dbp-vid'
		],
		'text'         => [
			'label'         => 'Text',
			'fileset_types' => [ 'text_json' ],
			'group'         => 'dbp-prod'
		]
	];

	/**
	 * Retrieves the media types supported by the filesets endpoint.
	 *
	 * @return array Returns an array of media types supported by the filesets endpoint.
	 * @throws BibleBrainsException If the request is unsuccessful and returns an error.
	 */
	public function all() {
		return $this->media_types;
	}

	/**
	 * Retrieves the available media type options.
	 *
	 * This method retrieves an array of media type options based on the media types supported by the filesets endpoint.
	 * It filters the options by a whitelist of predefined media types and adds a "Text" option at the end.
	 * The options are sorted by their value in ascending order.
	 *
	 * @return array Returns an array of available media type options.
	 *               Each option is represented as an associative array with the following keys:
	 *               - 'value': The value of the media type option.
	 *               - 'itemText': The text to be displayed for the media type option.
	 *
	 * @throws BibleBrainsException If the request to retrieve the media types from the filesets endpoint is unsuccessful
	 *                             or returns an error.
	 */
	public function media_type_options() {
		return collect( $this->all() )
			->map( function ( $data, $value ) {
				return [
					'value'    => $value,
					'itemText' => $data['label']
				];
			} )->all();
	}

	/**
	 * @param $media_type
	 *
	 * @return \Closure|null
	 * @throws BibleBrainsException
	 */
	public function find( $media_type ) {
		return collect( $this->all() )
			->filter( function ( $data, $value ) use ( $media_type ) {
				return $value === $media_type;
			} )
			->first();
	}
}
