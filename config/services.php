<?php

/**
 * @var $config DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Plugin\Providers\AdminServiceProvider;
use DT\Plugin\Providers\ConfigServiceProvider;
use DT\Plugin\Providers\MagicLinkServiceProvider;
use DT\Plugin\Providers\OptionsServiceProvider;
use DT\Plugin\Providers\RouteServiceProvider;
use DT\Plugin\Providers\ViewServiceProvider;
use DT\Plugin\Providers\AssetServiceProvider;

$config->merge( [
    'services' => [
        'providers' => [
            ConfigServiceProvider::class,
            OptionsServiceProvider::class,
            AssetServiceProvider::class,
            ViewServiceProvider::class,
            RouteServiceProvider::class,
            MagicLinkServiceProvider::class,
            AdminServiceProvider::class
        ],
        'tgmpa' => [
            'plugins' => [
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
                ]
            ],
            'config' => [
                'id'           => 'disciple_tools',
                'default_path' => '/partials/plugins/',
                'menu'         => 'tgmpa-install-plugins',
                'parent_slug'  => 'plugins.php',
                'capability'   => 'manage_options',
                'has_notices'  => true,
                'dismissible'  => true,
                'dismiss_msg'  => 'These are recommended plugins to complement your Disciple.Tools system.',
                'is_automatic' => true,
                'message'      => '',
            ],
        ]
    ]
]);
