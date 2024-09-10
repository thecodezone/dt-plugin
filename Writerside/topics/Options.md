# Options

WordPress options can be a bit of a pain. The framework provides a convenience helper for working with plugin options to simplify the API. 

The helpers are used to interact with plugin options. It provides methods for getting, setting, and deleting options. 

<tip>
This functionality is built on <a href="https://thecodezone.github.io/wp-support/options.html">thecodezone/wp-support</a>. Refer to it for the class-based API. 
</tip>

## Configuring default values

```php
$config->merge( [
    'options' => [
        'prefix' => 'my',
        'defaults' => [
            'background_color' => 'green,
            ...
        ],
    ]
] );
```

## Getting plugin options

In this example, assuming your plugin prefix is `my`, the following option would be fetched: `my_background_color`. The default value would be retrieved from `options.defaults.background_color`.

```php 
$background_color = get_plugin_option('background_color');
```

### Manually supplying a default value

You many override the configured default value: 

```php 
$background_color = get_plugin_option('background_color', 'blue');
```

## Setting plugin options

In this example, assuming your plugin prefix is `my`, the following option would be created or updated: `my_background_color`.
```php 
$background_color = set_plugin_option('background_color', 'red');
```