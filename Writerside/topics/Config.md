# Config

The plugin configuration keeps code-diving to a minimum by keeping a common place for storing configuration values. 

<tip>
 See <a href="https://thecodezone.github.io/wp-support/config.html">thecodezone/wp-support</a>
</tip>

## Retrieve a config value

```PHP
$color = config( 'app.background_color' );
```

### Retrieve a config value with a default value

```PHP
$color = config( 'app.background_color', 'red' );
```

## Set a config value on-the-fly

```PHP
set_config( 'app.background_color', 'purple' );
```

## Retrieve the underlying config instance

```PHP
$config = config();
$array = $config->to_array();
```