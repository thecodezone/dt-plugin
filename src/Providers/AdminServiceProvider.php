<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\Services\Settings;

class AdminServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {

    public function boot(): void
    {
        add_action( 'wp_loaded', [ $this, 'wp_loaded' ] );

        $this->getContainer()->get(Settings::class);
    }

    public function wp_loaded(): void
    {
        /*
       * Array of plugin arrays. Required keys are name and slug.
       * If the source is NOT from the .org repo, then source is also required.
       */
        $plugins = [
            [
                'name'     => 'Disciple.Tools Dashboard',
                'slug'     => 'disciple-tools-dashboard',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-dashboard/releases/latest/download/disciple-tools-dashboard.zip',
                'required' => false,
            ],
            [
                'name'     => 'Disciple.Tools Genmapper',
                'slug'     => 'disciple-tools-genmapper',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-genmapper.zip',
                'required' => true,
            ],
            [
                'name'     => 'Disciple.Tools Autolink',
                'slug'     => 'disciple-tools-autolink',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-autolink.zip',
                'required' => true,
            ],
        ];

        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = [
            'id'           => 'disciple_tools',
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
            'dismiss_msg'  => 'These are recommended plugins to complement your Disciple.Tools system.',
            // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,
            // Automatically activate plugins after installation or not.
            'message'      => '',
            // Message to output right before the plugins table.
        ];

        tgmpa( $plugins, $config );
    }

    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {

    }
}
