<?php

namespace DT\Plugin\MagicLinks;


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

	public function boot() {
		// Do whatever you want to do when the magic link URL is visited.
	}
}