<?php

namespace DT\Plugin\Services;

use DT\Plugin\CodeZone\Router;
use DT\Plugin\Illuminate\Http\Response;
use DT\Plugin\Illuminate\Support\Str;
use function DT\Plugin\Kucrut\Vite\enqueue_asset;
use function DT\Plugin\namespace_string;
use function DT\Plugin\plugin_path;
use function DT\Plugin\view;
use const DT\Plugin\Kucrut\Vite\VITE_CLIENT_SCRIPT_HANDLE;

class Template {

	/**
	 * Allow access to blank template
	 * @return bool
	 */
	public function blank_access(): bool {
		return true;
	}

	/**
	 * Start with a blank template
	 * @return void
	 */
	public function template_redirect(): void {
		$path = get_theme_file_path( 'template-blank.php' );
		include $path;
		die();
	}

	/**
	 * Enqueue CSS and JS assets
	 * @return void
	 */
	public function wp_enqueue_scripts(): void {
		enqueue_asset(
			plugin_path( '/dist' ),
			'resources/js/plugin.js',
			[
				'handle'    => 'dt-plugin',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => false, // Optional. Defaults to false.
			]
		);
		$this->whitelist_vite();
		$this->filter_asset_queue();
		wp_localize_script( 'dt-plugin', '$dt_plugin', [
			'nonce' => wp_create_nonce( 'dt-plugin' ),
		] );
	}


	private function whitelist_vite() {
		global $wp_scripts;
		global $wp_styles;

		$scripts = [];
		$styles  = [];

		foreach ( $wp_scripts->registered as $script ) {
			if ( $this->is_vite_asset( $script->handle ) ) {
				$scripts[] = $script->handle;
			}
		}

		add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed ) use ( $scripts ) {
			return array_merge( $allowed, $scripts );
		} );

		foreach ( $wp_styles->registered as $style ) {
			if ( $this->is_vite_asset( $style->handle ) ) {
				$styles[] = $style->handle;
			}
		}

		add_filter( namespace_string( 'allowed_styles' ), function ( $allowed ) use ( $styles ) {
			return array_merge( $allowed, $styles );
		} );
	}

	/**
	 * Determines if the given asset handle is allowed.
	 *
	 * This method checks if the provided asset handle is contained in the list of allowed handles.
	 * Allows the Template script file and the Vite client script file for dev use.
	 *
	 * @param string $asset_handle The asset handle to check.
	 *
	 * @return bool True if the asset handle is allowed, false otherwise.
	 */
	private function is_vite_asset( $asset_handle ) {
		if ( Str::contains( $asset_handle, [
			'dt-plugin',
			VITE_CLIENT_SCRIPT_HANDLE
		] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Reset asset queue
	 * @return void
	 */
	private function filter_asset_queue() {
		global $wp_scripts;
		global $wp_styles;

		$whitelist = apply_filters( namespace_string( 'allowed_scripts' ), [] );
		foreach ( $wp_scripts->registered as $script ) {
			if ( in_array( $script->handle, $whitelist ) ) {
				continue;
			}
			wp_dequeue_script( $script->handle );
		}

		$whitelist = apply_filters( namespace_string( 'allowed_styles' ), [] );
		foreach ( $wp_styles->registered as $style ) {
			if ( in_array( $script->handle, $whitelist ) ) {
				continue;
			}
			wp_dequeue_style( $style->handle );
		}
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
	 * @param $template
	 * @param $data
	 *
	 * @return mixed
	 */
	public function render( $template, $data ) {
		add_action( Router\namespace_string( 'render' ), [ $this, 'render_response' ], 10, 2 );
		add_filter( 'dt_blank_access', [ $this, 'blank_access' ] );
		add_action( 'dt_blank_head', [ $this, 'header' ] );
		add_action( 'dt_blank_footer', [ $this, 'footer' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 1000 );

		return view()->render( $template, $data );
	}

	public function render_response( Response $response ) {
		if ( apply_filters( 'dt_blank_access', false ) ) {
			add_action( 'dt_blank_body', function () use ( $response ) {
				// phpcs:ignore
				echo $response->getContent();
			}, 11 );
		} else {
			$response->send();
		}
	}

	/**
	 * Render the footer
	 * @return void
	 */
	public function footer() {
		wp_footer();
	}
}
