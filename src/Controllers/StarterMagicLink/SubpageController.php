<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT_Magic_URL;
use function DT\Plugin\template;

class SubpageController {
    public function show( ServerRequestInterface $request, $options ) {
        $user     = wp_get_current_user();
        $key      = sanitize_text_field( wp_unslash( $options['key'] ) );
        $home_url = DT_Magic_URL::get_link_url( 'starter', 'app', $key );

        return template( 'starter-magic-link/subpage', compact(
            'user',
            'home_url'
        ) );
    }
}
