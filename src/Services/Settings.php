<?php

namespace DT\Plugin\Services;

use DT\Plugin\CodeZone\WPSupport\Router\RouteInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\container;
use function DT\Plugin\namespace_string;
use function DT\Plugin\routes_path;

/**
 * Class settings
 *
 * The Settings class is responsible for adding the
 * settings page to the WordPress admin area.
 * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
 */
class Settings {

    /**
     * Register the admin menu.
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'register_menu' ], 99 );
    }

    /**
     * Register the admin menu
     *
     * @return void
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     */
    public function register_menu(): void {
        $menu = add_submenu_page( 'dt_extensions',
            __( 'DT Plugin', 'dt-plugin' ),
            __( 'DT Plugin', 'dt-plugin' ),
            'manage_dt',
            'dt-plugin',
            [ $this, 'route' ]
        );

        add_filter(namespace_string( 'settings_tabs' ), function ( $menu ) {
            $menu[] = [
                'label' => __( 'General', 'dt-plugin' ),
                'tab' => 'general'
            ];

            return $menu;
        }, 10, 1);

        add_action( 'load-' . $menu, [ $this, 'load' ] );
    }

    /**
     * Loads the necessary scripts and styles for the admin area.
     *
     * This method adds an action hook to enqueue the necessary JavaScript when on the admin area.
     * The JavaScript files are enqueued using the `admin_enqueue_scripts` action hook.
     *
     * @return void
     */
    public function load(): void
    {
        container()->get( Assets::class )->enqueue();
    }

    /**
     * Register the admin router.
     *
     * @return void
     */
    public function route(): void {
        $request = container()->get( ServerRequestInterface::class );
        $query = $request->getQueryParams();
        $tab = $query['tab'] ?? 'general';
        $route = container()->get( RouteInterface::class );
        $route->file( routes_path( 'settings.php' ) )
            ->resolve();
    }
}
