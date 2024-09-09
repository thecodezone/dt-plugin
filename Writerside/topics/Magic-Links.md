# Magic Links

<tip>
Learn more about <a href="https://disciple.tools/user-docs/magic-links/">magic links</a>.
</tip>

## Register a magic link

To register a magic link, add your Class Name to the `magic.links` config variable at `config/magic.php`. Note that your magic link should extend `DT_Magic_Url_Base`, however a simpler `DT\Plugin\MagicLinks\MagicLink.php` abstract is provided. 

```
$config->merge( [
    'magic' => [
        'links' => [
            ExampleMagicLink::class
        ]
    ]
] );

```

### Extending MagicLink

See [ExampleMagicLink.php](https://github.com/thecodezone/dt-plugin/blob/main/src/MagicLinks/MagicLink.php) for a simple implementation that includes routing. 

### Generating MagicLink URLs

You can use the `magic_url()` method to generate a magic URL. Example: 

```php
$magic_url = magic_url('example', 'link', get_user_id());
```

### Magic link routing

The example magic link includes a method for adding routes to a magic link, however you could easily include a file from the `/routes` folder from this method. 

It's important to place all magic routes within a route group with a route parameter of "key" to handle the magic key, which will be different for each user or post type. 

```php
$r->group( 'example/link/{key}', function ( RouteGroup $r ) {
    $r->get( '/', [ HomeController::class, 'show' ] );
    $r->get( '/subpage', [ SubpageController::class, 'show' ] );
} );
```

