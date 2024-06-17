<?php

namespace DT\Plugin\Services;

use const DT\Plugin\Kucrut\Vite\VITE_CLIENT_SCRIPT_HANDLE;

/**
 * Class AssetQueue
 *
 * This class provides methods to filter and manipulate asset queues in WordPress.
 */
class AssetQueue implements AssetQueueInterface
{
    /**
     * Filters scripts and styles based on whitelists.
     *
     * This method takes in two arrays, `$scripts_whitelist` and `$styles_whitelist`,
     * and filters the scripts and styles using the corresponding methods `filter_scripts`
     * and `filter_styles`. The `whitelist_vite_scripts` and `whitelist_vite_styles` methods
     * are used to retrieve the whitelisted Vite script and style handles respectively, by passing
     * the whitelists to them. The filtered scripts and styles are then passed to their respective
     * methods for further processing.
     *
     * @param array $scripts_whitelist The whitelist array for scripts.
     * @param array $styles_whitelist The whitelist array for styles.
     * @return void
     */
    public function filter(array $scripts_whitelist, array $styles_whitelist ) {
       $this->filter_scripts( $this->whitelist_vite_scripts( $scripts_whitelist ) );
       $this->filter_styles( $this->whitelist_vite_styles( $styles_whitelist ) );
    }

    /**
     * Filters the scripts in the WordPress scripts queue based on a whitelist.
     *
     * @param array $whitelist An array of script handles to be whitelisted.
     *
     * @return void
     */
    public function filter_scripts( array $whitelist ) {
        global $wp_scripts;

        foreach ( $wp_scripts->queue as $key => $handle ) {
            if ( $this->in_whitelist( $handle, $whitelist ) ) {
                continue;
            }
            unset( $wp_scripts->queue[$key] );
        }
    }

    /**
     * Filters styles.
     *
     * This method iterates through all enqueued styles in the WordPress global
     * `$wp_styles` object and removes the styles that are not present in the
     * whitelist array. The `$whitelist` array contains the handles of the styles
     * that should be kept.
     *
     * @param array $whitelist The array of style handles to whitelist.
     */
    public function filter_styles( array $whitelist ) {
        global $wp_styles;

        foreach ( $wp_styles->queue as $key => $handle ) {
            if ( $this->in_whitelist( $handle, $whitelist ) ) {
                continue;
            }
            unset( $wp_styles->queue[$key] );
        }
    }

    /**
     * Whitelists Vite scripts.
     *
     * This method iterates through all registered scripts in the WordPress global `$wp_scripts`
     * object and adds the handles of Vite assets to the whitelist array. The `$whitelist` array
     * is then merged with the array of Vite script handles and returned.
     *
     * @param array $whitelist The whitelist array to merge with the Vite script handles.
     * @return array The merged array of `$whitelist` and the Vite script handles.
     */
    protected function whitelist_vite_scripts(array $whitelist ): array
    {
        global $wp_scripts;

        return $this->whitelist_vite( $wp_scripts->registered, $whitelist);
    }

    /**
     * Whitelists Vite styles.
     *
     * This method iterates through all registered styles in the WordPress global `$wp_styles`
     * object and adds the handles of Vite assets to the whitelist array. The `$whitelist` array
     * is then merged with the array of Vite style handles and returned.
     *
     * @param array $whitelist The whitelist array to merge with the Vite style handles.
     * @return array The merged array of `$whitelist` and the Vite style handles.
     */
    protected function whitelist_vite_styles(array $whitelist ): array
    {
        global $wp_styles;

        return $this->whitelist_vite( $wp_styles->registered, $whitelist);
    }


    /**
     * Whitelists Vite scripts or styles
     *
     * @param array $registered The array of registered scripts to check for Vite assets.
     * @param array $whitelist The whitelist array to merge with the Vite script handles.
     * @return array The merged array of `$whitelist` and the Vite script handles.
     */
    protected function whitelist_vite( array $registered, array $whitelist ): array
    {
        $vite_assets = [];

        foreach ( $registered as $asset ) {
            if ( $this->is_vite_asset( $asset->handle ) ) {
                $vite_assets[] = $asset->handle;
            }
        }

        return array_merge( $whitelist, $vite_assets );
    }

    /**
     * Checks if a given asset handle is a Vite asset.
     *
     * This method checks if the provided `$asset_handle` contains the handle of the Vite client script.
     * It returns a boolean value indicating whether the asset is a Vite asset or not.
     *
     * @param string $asset_handle The handle of the asset to be checked.
     *
     * @return bool True if the asset is a Vite asset, false otherwise.
     */
    protected function is_vite_asset( $asset_handle ) {
        return strpos( $asset_handle, VITE_CLIENT_SCRIPT_HANDLE ) !== false;
    }

    protected function in_whitelist( $handle, $whitelist )
    {
        $filteredArray = array_filter($whitelist, function ($value) use ($handle) {
            return strpos($handle, $value) === 0;
        });

        return count($filteredArray) > 0;
    }
}
