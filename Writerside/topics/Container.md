# Container

<tip>
See <a href="https://container.thephpleague.com/">League Container</a> for full documentation on the dependency injection container. 
</tip>

## Register service providers

New service providers may be registered at in `config/services.php` at `services.providers`. 

```PHP
 'services' => [
    'providers' => [
        ConfigServiceProvider::class,
        OptionsServiceProvider::class,
        AssetServiceProvider::class,
        ViewServiceProvider::class,
        RouteServiceProvider::class,
        MagicLinkServiceProvider::class,
        AdminServiceProvider::class
        ...
    ],
    ...
```