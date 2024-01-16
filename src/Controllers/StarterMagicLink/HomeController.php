<?php

namespace CodeZone\Bible\Controllers\StarterMagicLink;

use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Illuminate\Http\Response;
use DT_Magic_URL;
use function CodeZone\Bible\template;

class HomeController {
	public function show( Request $request, Response $response, $key ) {
		$user        = wp_get_current_user();
		$subpage_url = DT_Magic_URL::get_link_url( 'starter', 'app', $key ) . '/subpage';

		return template( 'starter-magic-link/show', compact(
			'user',
			'subpage_url'
		) );
	}

	public function data( Request $request, Response $response, $key ) {
		$user = wp_get_current_user();
		$data = [
			'user_login' => $user->user_login,
		];
		$response->setContent( $data );

		return $response;
	}
}
