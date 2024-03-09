<?php

namespace CodeZone\Bible\Services\BibleBrains;

use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;

/**
 * The Scripture class is responsible for handling scripture references and retrieving scripture content from a Bible object.
 *
 * @package YourPackage
 */
class Scripture {
	/**
	 * Initializes a new instance of the class.
	 *
	 * @param Bible $bibles The Bible service
	 * @param Reference $reference The Reference service
	 *
	 * @return void
	 */
	public function __construct( private Bibles $bibles, private Reference $reference ) {
	}
}
