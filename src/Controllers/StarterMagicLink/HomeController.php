<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT_Magic_URL;
use function DT\Plugin\template;
use function DT\Plugin\response;

class HomeController {
	public function show( ServerRequestInterface $request, $options ) {
		$user        = wp_get_current_user();
		$key         = sanitize_text_field( wp_unslash( $options['key'] ) );
		$subpage_url = DT_Magic_URL::get_link_url( 'starter', 'app', $key ) . '/subpage';

		return template( 'starter-magic-link/show', compact(
			'user',
			'subpage_url'
		) );
	}

	public function data( ServerRequestInterface $request ) {
		$user = wp_get_current_user();
		$data = [
			'user_login' => $user->user_login,
		];

		return response( $data );
	}
}
