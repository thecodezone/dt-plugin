<?php

namespace DT\Plugin\Services;

use function DT\Plugin\config;

/**
 * Class Rewrites
 *
 * This class provides methods for managing rewrite rules in WordPress.
 */
class Rewrites {
    /**
     * Flushes the rewrite rules for the current WordPress site.
     *
     * This method calls the `flush_rewrite_rules()` function which flushes the rewrite rules
     * for the current site, ensuring that any custom rewrite rules take effect immediately.
     *
     * @return void
     */
    public function flush() {
        flush_rewrite_rules();
    }

    /**
     * Checks if all the rewrite rules configured in the plugin exists.
     *
     * This method iterates over the rewrite rules defined in the 'routes.rewrites'
     * configuration and checks if each rule exists in the system. If any of the rules
     * do not exist, the method will return false. If all rules exist, it will return true.
     *
     * @return bool Returns true if all rewrite rules exist, false otherwise.
     */
    public function has_latest()
    {
        foreach( config('routes.rewrites') as $regex => $query) {
            if( ! $this->exists( $regex, $query ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a given rewrite rule exists.
     *
     * @param string $rule The rewrite rule to check.
     * @param string|null $query The query string associated with the rule (optional).
     *
     * @return bool Returns true if the given rewrite rule exists, false otherwise.
     *
     * @global WP_Rewrite $wp_rewrite The global WP_Rewrite object.
     *
     * @see flush_rewrite_rules() To flush the rewrite rules.
     */
    public function exists($rule, $query = null) {
        global $wp_rewrite;
        $rules = $wp_rewrite->wp_rewrite_rules();
        if (!isset($rules[$rule])) {
            return false;
        }
        if ($query && $rules[$rule] !== $query) {
            return false;
        }
        return true;
    }

    /**
     * Adds a custom rewrite rule to the current WordPress site.
     *
     * This method calls the `add_rewrite_rule()` function which adds a custom rewrite rule
     * to the current site's rewrite rules array. The rule is defined by the provided `$regex`
     * and `$query` parameters. The rule will be added to the top of the rewrite rules list,
     * meaning it will be processed before any other rules.
     *
     * @param string $regex The regex pattern that the URL will be matched against.
     * @param string $query The query string or template that will be used to fulfill the request.
     * @return void
     */
    public function add($regex, $query) {
        add_rewrite_rule(
            $regex,
            $query,
            'top'
        );
    }

    /**
     * Applies the rewrite rules defined in the config file.
     *
     * This method iterates through the rewrite rules defined in the 'routes.rewrites' configuration
     * and adds them using the `add()` method of the current object.
     *
     * @return void
     */
    public function apply() {
        foreach (config('routes.rewrites') as $regex => $query) {
            $this->add($regex, $query);
        }
    }

    /**
     * Syncs the data between the current system and the latest version.
     *
     * This method checks if the current system has the latest data. If it does, then no action is taken.
     * If the current system does not have the latest data, it calls the `refresh()` method to update and sync the data.
     *
     * @return void
     */
    public function sync() {
        if( $this->has_latest() ) {
            $this->apply();
            return;
        }

        $this->refresh();
    }

    /**
     * Refreshes the rewrite rules for the current WordPress site.
     *
     * This method calls the `flush()` method to flush the rewrite rules
     * for the current site, ensuring that any custom rewrite rules take effect immediately.
     * It then calls the `apply()` method to apply any necessary changes.
     *
     * @return void
     */
    public function refresh() {
        $this->flush();
        $this->apply();
    }

    /**
     * Dumps the current rewrite rules for the current WordPress site.
     *
     * This method retrieves the global `$wp_rewrite` object and calls its `wp_rewrite_rules()`
     * method to get the current rewrite rules for the site. It then uses the `dd()` function
     * to dump the result and display it on the screen.
     *
     * @return void
     */
    public function dump() {
        global $wp_rewrite;
        dd($wp_rewrite->wp_rewrite_rules());
    }
}
