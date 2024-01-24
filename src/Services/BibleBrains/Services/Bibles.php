<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

class Bibles extends Service {
	function copyright( $id, $params = [] ) {
		return $this->http->get( 'bibles/' . $id . '/copyright', $params );
	}
}
