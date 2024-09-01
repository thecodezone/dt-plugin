<?php

namespace DT\Plugin\MagicLinks;

use DT\Plugin\Controllers\StarterMagicLink\HomeController;
use DT\Plugin\Controllers\StarterMagicLink\SubpageController;
use DT\Plugin\League\Route\RouteGroup;
use DT\Plugin\League\Route\Router;

/**
 * Class ExampleMagicLink
 *
 * This class extends the MagicLink class and represents an example of a magic link implementation.
 */
class ExampleMagicLink extends MagicLink {
    public $page_title = 'Magic Link';
    public $page_description = 'An example user-based magic link.';
    public $root = 'example';
    public $type = 'link';
    public $post_type = 'user';
    public $show_bulk_send = false;
    public $show_app_tile = false;

    /**
     * Do any action before the magic link is bootstrapped
     * @return void
     */
    public function init() {
        $this->whitelist_current_route();
    }


    /**
     * Do any action needed before the magic link is constructed.
     * @return void
     */
    public function boot() {
        $this->render();
    }

    public function routes( Router $r ) {
        $r->group( 'example/link/{key}', function ( RouteGroup $r ) {
            $r->get( '/', [ HomeController::class, 'show' ] );
            $r->get( '/subpage', [ SubpageController::class, 'show' ] );
        } );
    }
}
