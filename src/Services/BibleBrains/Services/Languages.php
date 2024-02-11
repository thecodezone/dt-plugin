<?php

namespace CodeZone\Bible\Services\BibleBrains\Services;

use function CodeZone\Bible\CodeZone\Router\collect;

class Languages extends Service {
	public function search( $name, $params = [] ) {

		$params = array_merge( [
			'include_translations' => false,
			'include_all_names'    => false,
		], $params );

		return $this->http->get( 'languages/search/' . $name, $params );
	}

	public function all_pages( $params = [] ) {
		$page        = 0;
		$response    = $this->all( $params );
		$data        = collect( $response->collect()->get( 'data' ) );
		$total_pages = $response->collect()->get( 'meta' )['pagination']['total_pages'] ?? 0;
		while ( $total_pages > $page ) {
			$page++;
			$data->push( ...$this->all( array_merge( $params, [ 'page' => $page ] ) )->collect()->get( 'data' ) );
		}

		return $data;
	}

	public function all( $params = [] ) {
		$params = array_merge( [
			'include_translations' => false,
			'include_all_names'    => false,
			'limit'                => 500,
		], $params );

		return $this->http->get( 'languages', $params );
	}
}
