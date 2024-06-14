<?php

namespace DT\Plugin\Services;

use function DT\Plugin\set_option;

/**
 * Class Options
 *
 * This class provides methods for retrieving options from the database.
 * Keys are scoped to the plugin to avoid conflicts with other plugins.
 * Default values may be provided for each option to avoid duplication.
 */
class Options {

    /**
     * The default option values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * The prefix to use for the option keys.
     *
     * @var string
     */
    protected $prefix;


    public function __construct( $defaults, $prefix )
    {
        $this->defaults = $defaults;
        $this->prefix = $prefix;
    }

    /**
     * Returns an array of default option values.
     *
     * @return array An associative array of default option values.
     */
    private function defaults(): array {
        return $this->defaults;
    }

    /**
     * Determines the scope key for a given key.
     *
     * @param string $key The key for which to determine the scope key.
     *
     * @return string The scope key for the given key.
     */
    public function scope_key( string $key ): string {
        return "{$this->prefix}_{$key}";
    }

    /**
     * Retrieves the value of the specified option.
     *
     * @param string $key The key of the option to retrieve.
     * @param mixed|null $default The default value to return if the option is not found. Default is null.
     *
     * @return mixed The value of the option if found, otherwise returns the default value.
     */
    public function get( string $key, mixed $default = null, $required = false ) {
        $defaults = $this->defaults();

        if ( $default !== null ) {
            $default = isset( $defaults[$key] ) ? $defaults[$key] : null;
        }

        $key = $this->scope_key( $key );


        $result = get_option( $key, $default );


        if ( $required && ! $result ) {
            $this->set( $key, $default );

            return $default;
        }

        return $result;
    }

    /**
     * Sets the value of the specified option.
     *
     * @param string $key The key of the option to set.
     * @param mixed $value The value to set for the option.
     *
     * @return bool Returns true if the option was set successfully, otherwise returns false.
     */
    public function set( string $key, mixed $value ): bool {
        $key = $this->scope_key( $key );

        return set_option( $key, $value );
    }
}
