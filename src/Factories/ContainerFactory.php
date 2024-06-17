<?php

namespace DT\Plugin\Factories;

use DT\Plugin\League\Container\Container;
use DT\Plugin\League\Container\ReflectionContainer;

/**
 * Class ContainerFactory
 *
 * The ContainerFactory class is responsible for creating and managing instances of the Container class.
 *
 * @see https://container.thephpleague.com/4.x/
 */
class ContainerFactory {
    private static Container $instance;


    /**
     * Returns a singleton instance of the Container class.
     *
     * This method checks if the singleton instance of the Container class exists. If not, it creates a new instance
     * using the `make()` method and assigns it to the `$instance` static property. Subsequent calls to this method will
     * return the same singleton instance.
     *
     * @return Container The singleton instance of the Container class.
     */
    public static function singleton(): Container {
        if ( ! isset( self::$instance ) ) {
            self::$instance = self::make();
        }
        return self::$instance;
    }

    /**
     * Creates and returns a new instance of the Container class.
     *
     * This method creates a new instance of the Container class, initializes it with a "delegate"
     * using the ReflectionContainer class, and returns the created container.
     *
     * @return Container A new instance of the Container class.
     */
    public static function make() {
        $container = new Container();
        $container->delegate( new ReflectionContainer() );
        return $container;
    }
}
