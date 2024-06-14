<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\MagicLinks\ExampleMagicLink;
use DT\Plugin\Psr\Container\ContainerExceptionInterface;
use DT\Plugin\Psr\Container\NotFoundExceptionInterface;
use function DT\Plugin\namespace_string;

class MagicLinkServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	protected $container;

    /**
     * List of magic links to register
     */
	protected $magic_links = [
		ExampleMagicLink::class,
	];

    /**
     * Provide the magic links, plus the magic_links var
     */
    public function provides( string $alias ): bool
    {
        return \in_array($alias, [
            namespace_string( 'magic_links' ),
            ...$this->magic_links
        ], \true);
    }


    /**
     * Register the magic links array and the magic link classes.
     *
     * When WordPress is loaded, init the magic links.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(): void
    {

        add_action( 'wp_loaded', [ $this, 'wp_loaded' ], 20 );


        $this->getContainer()->add( namespace_string( 'magic_links' ), function () {
            return apply_filters( namespace_string( 'magic_links' ), $this->magic_links );
        } );

        foreach ( $this->getContainer()->get( namespace_string( 'magic_links' ) ) as $magic_link ) {
            $this->getContainer()->addShared( $magic_link, function () use ( $magic_link ) {
                return new $magic_link();
            } );
        }
    }

    /**
     * {@inheritdoc}
     */
	public function register(): void {
        // The magic links are eager-loaded in the boot method
	}

    /**
     * Initialize the magic links
     */
    public function wp_loaded() {
        $magic_links = $this->container->get( namespace_string( 'magic_links' ) );

        foreach ( $magic_links as $magic_link ) {
            $this->container->get( $magic_link );
        }
    }
}
