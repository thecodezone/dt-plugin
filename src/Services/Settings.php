<?php

namespace DT\Plugin\Services;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\container;
use function DT\Plugin\namespace_string;

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
            __( 'DT Plugin', 'dt_plugin' ),
            __( 'DT Plugin', 'dt_plugin' ),
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
        $page = sanitize_text_field( wp_unslash( $query['page'] ?? '' ) );
        $tab = sanitize_text_field( wp_unslash( $query['tab'] ?? '' ) );
        $uri = '/wp-admin/' . trim( $page . '/' . $tab, '/' );

        $route = container()->get( Route::class );
        $route->as_uri( $uri )
            ->from_route_file( 'settings.php' )
            ->dispatch()
            ->render();
    }


    /**
     * Boot the plugin
     *
     * This method checks if the current context is the admin area and then
     * registers the required plugins using TGMPA library.
     *
     * @return void
     */
    public function boot(): void {
        /*
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = [
            [
                'name'     => 'Disciple.Tools Dashboard',
                'slug'     => 'disciple-tools-dashboard',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-dashboard/releases/latest/download/disciple-tools-dashboard.zip',
                'required' => false,
            ],
            [
                'name'     => 'Disciple.Tools Genmapper',
                'slug'     => 'disciple-tools-genmapper',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-genmapper.zip',
                'required' => true,
            ],
            [
                'name'     => 'Disciple.Tools Autolink',
                'slug'     => 'disciple-tools-autolink',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-autolink.zip',
                'required' => true,
            ],
        ];

        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = [
            'id'           => 'disciple_tools',
            // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '/partials/plugins/',
            // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins',
            // Menu slug.
            'parent_slug'  => 'plugins.php',
            // Parent menu slug.
            'capability'   => 'manage_options',
            // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,
            // Show admin notices or not.
            'dismissable'  => true,
            // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => 'These are recommended plugins to complement your Disciple.Tools system.',
            // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,
            // Automatically activate plugins after installation or not.
            'message'      => '',
            // Message to output right before the plugins table.
        ];

        tgmpa( $plugins, $config );
    }
}
