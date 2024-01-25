<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

class Languages extends Service {
	public function all( $params = [] ) {
		$params = array_merge( [
			'include_translations' => false,
			'limit'                => 1000,
		], $params );

		return $this->http->get( 'languages', $params );
	}
}
