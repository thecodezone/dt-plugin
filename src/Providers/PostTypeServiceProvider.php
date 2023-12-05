<?php

namespace DT\Plugin\Providers;

use DT\Plugin\PostTypes\StarterPostType;

class PostTypeServiceProvider extends ServiceProvider {

	public function register(): void {
	}

	public function boot(): void {
		add_filter( 'dt_post_type_modules', [ $this, 'dt_post_type_modules' ], 20, 1 );
		$this->container->make( StarterPostType::class );
	}

	/**
	 * Register the post type modules
	 * @return array
	 */
	public function dt_post_type_modules(): array {
		$modules['starter_base'] = [
			'name'          => __( 'Starter', 'disciple-tools-plugin-starter-template' ),
			'enabled'       => true,
			'locked'        => true,
			'prerequisites' => [ 'contacts_base' ],
			'post_type'     => 'starter_post_type',
			'description'   => __( 'Default starter functionality', 'disciple-tools-plugin-starter-template' )
		];

		return $modules;
	}
}
