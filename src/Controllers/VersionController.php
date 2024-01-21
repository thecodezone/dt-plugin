<?php

namespace CodeZone\Bible\Controllers;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;

class VersionController {
	public function index( Request $request, Response $response ) {
		$languages = $request->get( 'languages' );

		if ( random_int( 0, 1 ) === 1 ) {
			return [
				[
					'bible_id'   => 'ORYLPF',
					'bible_name' => 'Easy to Read Version'
				],
				[
					'bible_id'   => 'CJAWYI',
					'bible_name' => 'Cham, Western New Testament'
				]
			];
		}

		return [
			[
				'bible_id'   => 'ORYLPF',
				'bible_name' => 'Wycliffe Bible Translators, Inc.'
			],
			[
				'bible_id'   => 'RIFNVL',
				'bible_name' => 'Latin Script'
			]
		];
	}
}
