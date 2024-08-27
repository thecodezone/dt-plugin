<?php

namespace DT\Plugin\Services;

use DT\Plugin\CodeZone\WPSupport\Assets\AssetQueue;
use DT\Plugin\CodeZone\WPSupport\Assets\AssetQueueInterface;
use function DT\Plugin\config;
use function DT\Plugin\Kucrut\Vite\enqueue_asset;
use function DT\Plugin\namespace_string;

/**
 * Class Assets
 *
 * This class is responsible for registering necessary actions for enqueueing scripts and styles,
 * whitelisting specific assets, and providing methods for enqueueing scripts and styles for the frontend and admin area.
 *
 * @see https://github.com/kucrut/vite-for-wp
 *
 */
class Assets
{
	/**
	 * AssetQueue Service.
	 *
	 * @var AssetQueue $asset_queue The AssetQueue instance.
	 */
	private AssetQueueInterface $asset_queue;

	/**
	 * Flag indicating whether a resource has been enqueued.
	 *
	 * @var bool $enqueued False if the resource has not been enqueued, true otherwise.
	 */
	private static bool $enqueued = false;

	public function __construct( AssetQueueInterface $asset_queue )
	{
		$this->asset_queue = $asset_queue;
	}

	/**
	 * Register method to add necessary actions for enqueueing scripts
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( self::$enqueued ) {
			return;
		}
		self::$enqueued = true;

		if ( !is_admin() ) {
			add_action( 'wp_print_styles', [ $this, 'wp_print_styles' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		}
	}
	/**
	 * Reset asset queue
	 *
	 * @return void
	 */
	public function wp_print_styles() {
		$this->asset_queue->filter(
			apply_filters( namespace_string( 'allowed_scripts' ), [] ),
			apply_filters( namespace_string( 'allowed_styles' ), [] )
		);
	}


	/**
	 * Enqueues scripts and styles for the frontend.
	 *
	 * This method enqueues the specified asset(s) for the frontend. It uses the "enqueue_asset" function to enqueue
	 * the asset(s) located in the provided plugin directory path with the given filename. The asset(s) can be JavaScript
	 * or CSS files. Optional parameters can be specified to customize the enqueue behavior.
	 *
	 * @return void
	 * @see https://github.com/kucrut/vite-for-wp
	 */
	public function wp_enqueue_scripts() {
		enqueue_asset(
			config( 'assets.manifest_dir' ),
			'resources/js/plugin.js',
			[
				'handle'    => 'dt-plugin',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => true, // Optional. Defaults to false.
			]
		);
		wp_localize_script( 'dt-plugin', config( 'assets.javascript_global_scope' ), apply_filters( namespace_string( 'javascript_globals' ), [] ) );
	}
}
