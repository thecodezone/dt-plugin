<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use CodeZone\Bible\Illuminate\Http\Client\Factory as Http;
use CodeZone\Bible\Illuminate\Http\Client\PendingRequest;

abstract class Service {

	/**
	 * @var Http
	 */
	protected PendingRequest $http;

	/**
	 * @param Http $http
	 */
	public function __construct( Http $http ) {
		$this->http = $http->bibleBrains();
	}
}
