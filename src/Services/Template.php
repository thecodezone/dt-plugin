<?php

namespace CodeZone\Bible\Services;

use CodeZone\Bible\League\Plates\Extension\Asset;
use function CodeZone\Bible\container;
use function CodeZone\Bible\Kucrut\Vite\enqueue_asset;
use function CodeZone\Bible\plugin_path;
use function CodeZone\Bible\view;

class Template {
    /**
     * @var Assets
     */
    protected Assets $assets;

	public function __construct( Assets $assets ) {
        $this->assets = $assets;
	}

	/**
	 * Render the header
	 * @return void
	 */
	public function header() {
		wp_head();
	}

	/**
	 * Render the template
	 *
	 * @param string $template
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function render( $template, $data ) {
		$this->assets->enqueue();

		return view()->render( $template, $data );
	}

	/**
	 * Render the footer
	 * @return void
	 */
	public function footer() {
		wp_footer();
	}
}
