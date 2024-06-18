<?php

namespace CodeZone\WPSupport\Options;

use function CodeZone\WPSupport\Services\add_option;
use function CodeZone\WPSupport\Services\get_option;
use function CodeZone\WPSupport\Services\update_option;

/**
 * Class Options
 *
 * This class provides methods for retrieving options from the database.
 * Keys are scoped to the plugin to avoid conflicts with other plugins.
 * Default values may be provided for each option to avoid duplication.
 */
class Options implements OptionsInterface
{

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
    protected function defaults(): array {
        return $this->defaults;
    }

    protected function get_default( $key ) {
        return $this->defaults()[$key] ?? null;
    }

    /**
     * Determines the scope key for a given key.
     *
     * @param string $key The key for which to determine the scope key.
     *
     * @return string The scope key for the given key.
     */
    protected function scope_key( string $key ): string {
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
    public function get( string $key, $default = null, $required = false, $scoped = true ) {
        $defaults = $this->defaults();

        if ( $default === null ) {
            $default = $this->get_default( $key );
        }

        if ( $scoped ) {
            $key = $this->scope_key( $key );
        }

        $result = \get_option( $key, $default );


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
    public function set( string $key, $value, $scoped = true ): bool {
        if ( $scoped ) {
            $key = $this->scope_key( $key );
        }

        if ( \get_option( $key ) === false ) {
            return \add_option( $key, $value );
        } else {
            return \update_option( $key, $value );
        }
    }
}
