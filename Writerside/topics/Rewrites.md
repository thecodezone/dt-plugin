# Routing

<tip>
    See <a href="https://route.thephpleague.com/">League Router</a> for full documentation on the routing system including controllers, service providers and middleware.
</tip>

<tip>
    See <a href="https://thecodezone.github.io/wp-support/route.html">thecodezone/wp-support</a> for documentation on working with the Route service. 
</tip>

## Registering routes

Route files are stored in the `routes` folder. 

```php
$r->get( '/hello', [ HelloController::class, 'show' ] );
```

## Registering new route files

New route files may be registered in `config/routes.php`. 

```php
'routes' => [
    ...
    'files' => [
        'api' => [
            "file" => "api.php",
            'query' => 'dt-plugin-api',
            'path' => 'dt/plugin/api',
        ],
        'web' => [
            "file" => "web.php",
            'query' => 'dt-plugin',
            'path' => 'dt/plugin',
        ]
        ...
    ],
    ...
```

## Middleware


### Registering Middleware

Middleware is registered in the `config/routes.php` file. 

```PHP
...
 'middleware' => [
    CustomMiddleware::class,
    ...
],
...
```

### Provided Middleware

#### Nonce Middleware

<tip>
See documentation at <a href="https://thecodezone.github.io/wp-support/nonce-middleware.html">thecodezone/wp-support</a>
</tip>

#### HasCap Middleware

<tip>
See documentation at <a href="https://thecodezone.github.io/wp-support/hascap.html">thecodezone/wp-support</a>
</tip>

#### Logged In Middleware

Redirect the user if the user is logged out. 

```
$r->get( '/hello', [ HelloController::class, 'show' ] )
    ->middleware( LoggedIn() );
```

#### Logged Out Middleware

Redirect the user if the user is logged in.

```
$r->get( '/hello', [ HelloController::class, 'show' ] )
    ->middleware( LoggedOut() );
```

## Rewrites

The framework utilizes WordPress' rewrite functionality to integrate [FastRoute](https://github.com/nikic/FastRoute) with WordPress.

<tip>
See WordPress' <a href="https://developer.wordpress.org/reference/functions/add_rewrite_rule/">documentation</a> on adding rewrites.
</tip>

<tip>
See <a href="https://thecodezone.github.io/wp-support/rewrites.html">thecodezone/wp-supports</a> Rewrites service. 
</tip>

### Registering rewrites

Add rewrites to the `routes.rewrites` array to register them. 

```php
'routes' => [
        'rewrites' => [
            '^dt/plugin/api/?$' => 'index.php?dt-plugin-api=/',
            '^dt/plugin/api/(.+)/?' => 'index.php?dt-plugin-api=$matches[1]',
            '^dt/plugin/?$' => 'index.php?dt-plugin=/',
            '^dt/plugin/(.+)/?' => 'index.php?dt-plugin=$matches[1]',
        ],        
```

The query values in the rewrite values will then be matched to route file query values below: 

```php
'routes' => [
        'rewrites' => [
            '^dt/plugin/api/?$' => 'index.php?dt-plugin-api=/',
            '^dt/plugin/api/(.+)/?' => 'index.php?dt-plugin-api=$matches[1]',
            '^dt/plugin/?$' => 'index.php?dt-plugin=/',
            '^dt/plugin/(.+)/?' => 'index.php?dt-plugin=$matches[1]',
        ],
        'files' => [
            'api' => [
                "file" => "api.php",
                'query' => 'dt-plugin-api', //This matches the rewrite value above
                'path' => 'dt/plugin/api',
            ],
            'web' => [
                "file" => "web.php",
                'query' => 'dt-plugin', //This matches the rewrite value above
                'path' => 'dt/plugin',
            ]
        ],
```

Matching these values together allows us to sync our routing between FastRoute and WordPress.


