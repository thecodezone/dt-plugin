<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\CodeZone\Router\Middleware\Stack;
use function CodeZone\Bible\Kucrut\Vite\enqueue_asset;
use function CodeZone\Bible\plugin_path;

class AdminServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
	}

	/**
	 * Registers the Bible Reader menu page and submenu page.
	 *
	 * This method adds the Bible Reader menu page and submenu page to the WordPress admin menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		$menu = add_menu_page(
			__( 'Bible Reader', 'bible-reader' ),
			__( 'Bible Reader', 'bible-reader' ),
			'manage_options',
			'bible-reader',
			'',
			'dashicons-book-alt',
			99
		);

		$submenu = add_submenu_page(
			'bible-reader',
			__( 'Bible Reader', 'bible-reader' ),
			__( 'Bible Reader', 'bible-reader' ),
			'manage_options',
			'bible-reader',
			[ $this, 'register_router' ]
		);

		add_filter( 'bible_reader_settings_tabs', function ( $menu ) {
			$menu[] = [
				'label' => __( 'Biblical Text Setup', 'bible-reader' ),
				'tab'   => 'bible'
			];
			$menu[] = [
				'label' => __( 'Customization', 'bible-reader' ),
				'tab'   => 'customization'
			];
			$menu[] = [
				'label' => __( 'Support', 'bible-reader' ),
				'tab'   => 'support'
			];

			return $menu;
		}, 10, 1 );

		add_action( 'load-' . $menu, [ $this, 'load' ] );
	}

	/**
	 * Registers the router middleware.
	 *
	 * @return void
	 */
	public function register_router(): void {
		apply_filters( 'codezone/bible/middleware', $this->container->make( Stack::class ) )
			->run();
	}

	/**
	 * Loads the necessary scripts and styles for the admin area.
	 *
	 * This method adds an action hook to enqueue the necessary JavaScript when on the admin area.
	 * The JavaScript files are enqueued using the `admin_enqueue_scripts` action hook.
	 *
	 * @return void
	 */
	public function load(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
	}

	/**
	 * Add the admin head
	 *
	 * @return void
	 */
	public function admin_head(): void {
		?>
        <style>
            .br-cloak {
                display: none;
            }
        </style>
		<?php
	}

	/**
	 * Enqueue the admin assets
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
		enqueue_asset(
			plugin_path( '/dist' ),
			'resources/js/admin.js',
			[
				'handle'    => 'bible-reader-admin',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => false, // Optional. Defaults to false.
			]
		);
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
		add_action( 'admin_menu', [ $this, 'register_menu' ], 99 );

		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = [];

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = [
			'id'           => 'bible-reader',
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
			'dismiss_msg'  => 'These are recommended plugins to complement the Bible Reader plugin.',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,
			// Automatically activate plugins after installation or not.
			'message'      => '',
			// Message to output right before the plugins table.
		];

		tgmpa( $plugins, $config );
	}
}
