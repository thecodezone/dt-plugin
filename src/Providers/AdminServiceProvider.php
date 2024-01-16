<?php

namespace CodeZone\Bible\Providers;

use CodeZone\Bible\CodeZone\Router\Middleware\Stack;

class AdminServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 99 );
	}

	/**
	 * Register the admin menu
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'Reaching Asia', 'bible-reader' ),
			__( 'Reaching Asia', 'bible-reader' ),
			'manage_options',
			'bible-reader',
			'',
			'dashicons-book-alt',
			99
		);

		add_submenu_page(
			'bible-reader',
			__( 'Bible Reader', 'bible-reader' ),
			__( 'Bible Reader', 'bible-reader' ),
			'manage_options',
			'bible-reader',
			[ $this, 'register_router' ]
		);
	}

	/**
	 * Register the admin router using the middleware stack via filter.
	 *
	 * @return void
	 */
	public function register_router(): void {
		apply_filters( 'codezone/bible/middleware', $this->container->make( Stack::class ) )
			->run();
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
