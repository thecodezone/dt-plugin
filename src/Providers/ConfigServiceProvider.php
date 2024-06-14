<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Config\Configuration;
use DT\Plugin\League\Container\Exception\NotFoundException;
use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\Nette\Schema\Expect;
use DT\Plugin\Psr\Container\ContainerExceptionInterface;
use function DT\Plugin\plugin_path;

class ConfigServiceProvider extends AbstractServiceProvider {
    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return in_array( $id, [
            Configuration::class,
        ]);
    }

    /**
     * Register the configuration service.
     * @see https://config.thephpleague.com/
     * @throws NotFoundException|ContainerExceptionInterface
     */
    public function register(): void
    {
        $this->getContainer()->addShared(Configuration::class, function () {
            return new Configuration([
                'plugin' => Expect::structure([
                    'nonce_name' => Expect::string(),
                    'dt_version' => Expect::float(),
                    'home_route' => Expect::string(),
                ]),
                'services' => Expect::structure([
                    'providers' => Expect::listOf( Expect::string() ),
                    'tgmpa' => Expect::structure([
                        'plugins' => Expect::listOf(
                            Expect::structure( [
                                'name'     => Expect::string(),
                                'slug'     => Expect::string(),
                                'source'   => Expect::string(),
                                'required' => Expect::bool()
                            ] )
                        ),
                        'config' => Expect::array()
                    ]),
                ]),
                'options' => Expect::structure([
                    'prefix' => Expect::string(),
                    'defaults' => Expect::listOf( Expect::string() ),
                ]),
                'routes' => Expect::structure([
                    'files' => Expect::listOf( Expect::structure( [
                        "file" => Expect::string(),
                        'rewrite' => Expect::string(),
                        'query' => Expect::string(),
                    ] ) ),
                    'middleware' => Expect::listOf( Expect::string() ),
                ]),
                'assets' => Expect::structure([
                    'allowed_styles' => Expect::listOf( Expect::string() ),
                    'allowed_scripts' => Expect::listOf( Expect::string() ),
                    'javascript_global_scope' => Expect::string(),
                    'javascript_globals' => Expect::array(),
                    'manifest_dir' => Expect::string()
                ]),
                "magic" => Expect::structure([
                    'links' => Expect::listOf( Expect::string() ),
                ])
            ]);
        });


        $config = $this->getContainer()->get(Configuration::class);
        foreach (glob(plugin_path('config/*.php')) as $filename)
        {
            require_once $filename;
        }
    }
}
