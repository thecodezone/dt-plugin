<?php

namespace DT\Plugin\Services;

use function DT\Plugin\Kucrut\Vite\enqueue_asset;
use function DT\Plugin\namespace_string;
use function DT\Plugin\plugin_path;
use function DT\Plugin\view;
use const DT\Plugin\Kucrut\Vite\VITE_CLIENT_SCRIPT_HANDLE;

/**
 * Class Template
 *
 * This class represents a template in a web application. It is responsible for rendering the template and managing assets.
 */
class Template {

    /**
     * @var Assets
     */
    protected $assets;

    public function __construct( Assets $assets )
    {
        $this->assets = $assets;
    }

    /**
	 * Allow access to blank template
	 * @return bool
	 */
	public function blank_access(): bool {
		return true;
	}

    /**
     * Render the header
     */
	public function header() {
		wp_head();
	}

	/**
	 * Render the template
	 *
	 * @param $template
	 * @param $data
	 *
	 * @return mixed
	 */
	public function render( $template, $data ) {
		add_filter( 'dt_blank_access', [ $this, 'blank_access' ] );
		add_action( 'dt_blank_head', [ $this, 'header' ] );
		add_action( 'dt_blank_footer', [ $this, 'footer' ] );
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
