<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;

class MediaController {
	public function index( Request $request, Response $response ) {
		$languages = $request->get( 'translations' );

		switch ( random_int( 0, 4 ) ) {
			case 0:
				$media = [
					[
						'label' => 'Audio',
						'key'   => 'audio'
					]
				];
				break;
			case 1:
				$media = [
					[
						'label' => 'Video',
						'key'   => 'video'
					],
					[
						'label' => 'Text',
						'key'   => 'text'
					]
				];
				break;
			case 2:
				$media = [
					[
						'label' => 'Audio',
						'key'   => 'audio'
					],
					[
						'label' => 'Text',
						'key'   => 'text'
					]
				];
				break;
			default:
				$media = [
					[
						'label' => 'Audio',
						'key'   => 'audio'
					],
					[
						'label' => 'Video',
						'key'   => 'video'
					],
					[
						'label' => 'Text',
						'key'   => 'text'
					]
				];
				break;
		}

		return $media;
	}
}
