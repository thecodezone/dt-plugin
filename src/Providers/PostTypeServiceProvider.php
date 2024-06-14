<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\PostTypes\StarterPostType;
use DT\Plugin\PostTypes\WidgetPostType;
use function DT\Plugin\namespace_string;

class PostTypeServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
	}

	/**
	 * Do any setup needed after the theme is ready.
	 * DT is registered.
	 *
	 * @return void
	 */
	public function boot(): void {
        add_filter( 'dt_post_type_modules', [ $this, 'dt_post_type_modules' ], 1, 1 );
        add_action( 'wp_loaded', [ $this, 'wp_loaded' ], 20 );

        $this->getContainer()->addShared( WidgetPostType::class );
	}

    /**
     * Register the post types.
     *
     * @return void
     */
    public function wp_loaded(): void {
        $this->getContainer()->get( WidgetPostType::class );
    }

	/**
	 * Retrieves an array of post type modules.
	 *
	 * Each module is represented by an associative array with the following keys:
	 *   - 'name': The name of the module.
	 *   - 'enabled': A boolean value indicating whether the module is enabled or not.
	 *   - 'locked': A boolean value indicating whether the module is locked or not.
	 *   - 'prerequisites': An array of module names that this module depends on.
	 *   - 'post_type': The post type associated with the module.
	 *   - 'description': The description of the module.
	 *
	 * @return array An array of post type modules.
	 */
	public function dt_post_type_modules(): array {
		$modules[namespace_string('widget_base')] = [
			'name'          => __( 'Widgets', 'dt-plugin' ),
			'enabled'       => true,
			'locked'        => true,
			'prerequisites' => [ 'contacts_base' ],
			'post_type'     => namespace_string('widgets'),
			'description'   => __( 'Default starter functionality', 'dt-plugin' )
		];

		return $modules;
	}

    public function provides( string $id ): bool
    {
        return in_array($id, [
            WidgetPostType::class
        ]);
    }
}
