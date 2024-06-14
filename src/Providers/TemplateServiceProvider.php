<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Plates\Engine;
use DT\Plugin\Services\Plates\Escape;
use function DT\Plugin\namespace_string;
use function DT\Plugin\views_path;

/**
 * Class TemplateServiceProvider
 *
 * This class is a service provider responsible for registering the view engine singleton and any extensions.
 *
 * @see https://platesphp.com/
 */
class TemplateServiceProvider extends AbstractServiceProvider {

    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to zcheck.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return in_array($id, [
            Engine::class
        ]);
    }

	/**
	 * Register the view engine singleton and any extensions
	 *
	 * @return void
	 */
	public function register(): void {
        add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
            $allowed_css[] = 'dt-plugin';

            return $allowed_css;
        } );

        add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
            $allowed_js[] = 'dt-plugin';

            return $allowed_js;
        } );

        $this->getContainer()->addShared( Engine::class, function () {
            return new Engine( views_path() );
        } );
        $this->getContainer()->get( Engine::class );
	}
}
