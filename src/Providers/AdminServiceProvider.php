<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\CodeZone\Router\Middleware\Stack;
use CodeZone\Bible\Services\Assets;
use function CodeZone\Bible\Kucrut\Vite\enqueue_asset;
use function CodeZone\Bible\namespace_string;
use function CodeZone\Bible\plugin_path;

/**
 * Class AdminServiceProvider
 *
 * This class is responsible for handling administrative tasks and providing services for the admin area of the plugin.
 */
class AdminServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
	}

	/**
	 * Registers the Bible Plugin menu page and submenu page.
	 *
	 * This method adds the Bible Plugin menu page and submenu page to the WordPress admin menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		$menu = add_menu_page(
			__( 'The Bible Plugin', 'bible-plugin' ),
			__( 'The Bible Plugin', 'bible-plugin' ),
			'manage_options',
			'bible-plugin',
			'',
			'dashicons-book-alt',
			99
		);

		add_submenu_page(
			'bible-plugin',
			__( 'The Bible Plugin', 'bible-plugin' ),
			__( 'The Bible Plugin', 'bible-plugin' ),
			'manage_options',
			'bible-plugin',
			[ $this, 'register_router' ]
		);

		add_filter( namespace_string( 'settings_tabs' ), function ( $menu ) {
			$menu[] = [
				'label' => __( 'Biblical Text Setup', 'bible-plugin' ),
				'tab'   => 'bible'
			];
			$menu[] = [
				'label' => __( 'Customization', 'bible-plugin' ),
				'tab'   => 'customization'
			];
			$menu[] = [
				'label' => __( 'Support', 'bible-plugin' ),
				'tab'   => 'support',
				'href'  => 'https://support.thebibleplugin.com/'
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
		apply_filters( namespace_string( 'middleware' ), $this->container->make( Stack::class ) )
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
		$this->container->make( Assets::class )->enqueue();
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
			'id'           => 'bible-plugin',
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
			'dismiss_msg'  => 'These are recommended plugins to complement The Bible Plugin.',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,
			// Automatically activate plugins after installation or not.
			'message'      => '',
			// Message to output right before the plugins table.
		];

		tgmpa( $plugins, $config );
	}
}
