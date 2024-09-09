# Helpers

## Importing helpers

Helpers are namespaced to avoid collisions. The must be imported as follows:

```PHP
use function DT\Plugin\response;
$response = response();
```

## Provided helpers

The following helpers are provided. You may modify these helpers or add your own. 

- `function plugin(): Plugin` - Returns the singleton instance of the Plugin class.
- `function container(): Container` - Return the dependency injection container instance.
- `function config( $key = null, $default = null )` - Get a configuration value. 
- `function set_config( $key = null )` - Set a configuration value. 
- `function has_route_rewrite(): bool` - Checks if the route rewrite rule exists in the WordPress rewrite rules.
- `function plugin_url( string $path = '' ): string` - Retrieves the URL of a file or directory within the plugin directory.
- `function route_url( string $path = '', $key = 'web' ): string` - Returns the URL for a given route.
- `function api_url( string $path ) string` - Returns the URL of an API endpoint based on the given path.
- `function web_url( string $path ) string` - Returns the URL for a given web path.
- `function plugin_path( string $path = '' ): string` Returns the path of a plugin file or directory, relative to the plugin directory.
- `function src_path( string $path = '' ): string` Get the source path using the given path.
- `function resources_path( string $path = '' ): string` Returns the path to the resources directory.
- `function routes_path( string $path = '' ): string` Returns the path to the routes directory.
- `function views_path( string $path = '' ): string` Returns the path to the views directory.
- `function view( string $view = "", array $args = [] )` Renders a view and returns a response.
- `function template( string $template = "", array $args = [] )` Renders a template using the Template service and returns a response.
- `function request(): ServerRequestInterface` Returns the Request object.
- `function redirect( string $url, int $status = 302, $headers = [] ): ResponseInterface` Creates a new ResponseInterface instance for the given URL.
- `function response( $content, $status = 200, $headers = [] )` Returns a response object.
- `function set_option( string $option_name, $value ): bool` Set the value of an option, either adding or updating it. 
- `function get_plugin_option( $option, $default = null, $required = false )` Retrieves the prefixed option
- `function set_plugin_option( $option, $value ): bool` Sets the prefixed option
- `function transaction( $callback )` Start a database transaction. 
- `function namespace_string( string $string ): string` Concatenates the given string to the namespace of the Plugin class.
- `function magic_app( $root, $type )` Returns the registered magic apps for a specific root and type.
- `function magic_url( $root, $type, $id ): string ` Generates a magic URL for a given root, type, and ID.