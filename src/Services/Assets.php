<?php

namespace DT\Plugin\Services;

use function DT\Plugin\config;
use function DT\Plugin\Kucrut\Vite\enqueue_asset;
use function DT\Plugin\namespace_string;
use const DT\Plugin\Kucrut\Vite\VITE_CLIENT_SCRIPT_HANDLE;

/**
 * Class Assets
 *
 * This class is responsible for registering necessary actions for enqueueing scripts and styles,
 * whitelisting specific assets, and providing methods for enqueueing scripts and styles for the frontend and admin area.
 *
 * @see https://github.com/kucrut/vite-for-wp
 *
 */
class Assets {
    private static $enqueued = false;

    /**
     * Register method to add necessary actions for enqueueing scripts
     *
     * @see https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     * @see https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
     * @see https://developer.wordpress.org/reference/hooks/wp_print_styles/
     * @return void
     */
    public function enqueue() {
        if ( self::$enqueued ) {
            return;
        }
        self::$enqueued = true;

        if ( !is_admin() ) {
            add_action( 'wp_print_styles', [ $this, 'filter_assets' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
        }
    }

    public function filter_assets() {
        $this->whitelist_vite();
        $this->filter_asset_queue();
    }

    /**
     * Reset asset queue
     *
     * @return void
     */
    private function filter_asset_queue() {
        global $wp_scripts;
        global $wp_styles;

        $whitelist = apply_filters( namespace_string( 'allowed_scripts' ), [] );
        foreach ( $wp_scripts->queue as $key => $handle ) {
            if ( in_array( $handle, $whitelist ) ) {
                continue;
            }
            unset( $wp_scripts->queue[$key] );
        }
        $whitelist = apply_filters( namespace_string( 'allowed_styles' ), [] );
        foreach ( $wp_styles->queue as $key => $handle ) {
            if ( in_array($handle, $whitelist ) ) {
                continue;
            }
            unset( $wp_styles->queue[$key] );
        }

    }

    /**
     * Whitelist Vite assets for enqueueing scripts and adding cloaked styles
     *
     * @see https://github.com/kucrut/vite-for-wp
     * @return void
     */
    private function whitelist_vite() {
        global $wp_scripts;
        global $wp_styles;

        $scripts = [];
        $styles = [];

        foreach ( $wp_scripts->registered as $script ) {
            if ( $this->is_vite_asset( $script->handle ) ) {
                $scripts[] = $script->handle;
            }
        }

        // phpcs:ignore
        add_filter( namespace_string( 'allowed_scripts' ),
            function ( $allowed ) use ( $scripts ) {
                return array_merge( $allowed, $scripts );
            }
        );

        foreach ( $wp_styles->registered as $style ) {
            if ( $this->is_vite_asset( $style->handle ) ) {
                $styles[] = $style->handle;
            }
        }

        add_filter( namespace_string( 'allowed_styles' ),
            function ( $allowed ) use ( $styles ) {
                return array_merge( $allowed, $styles );
            }
        );
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
     * @see https://github.com/kucrut/vite-for-wp
     */
    private function is_vite_asset( $asset_handle ) {
        if ( strpos( $asset_handle, 'dt-plugin' ) !== false
            || strpos( $asset_handle, VITE_CLIENT_SCRIPT_HANDLE ) !== false ) {
            return true;
        }

        return false;
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
        wp_localize_script( 'dt-plugin', config('assets.javascript_global_scope'), apply_filters( namespace_string( 'javascript_globals' ), [] ) );
    }
}
