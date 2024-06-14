<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Laminas\Diactoros\Response;
use DT\Plugin\Laminas\Diactoros\ServerRequestFactory;
use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\League\Route\Http\Exception\NotFoundException;
use DT\Plugin\League\Route\Strategy\ApplicationStrategy;
use DT\Plugin\League\Route\Strategy\StrategyInterface;
use DT\Plugin\Psr\Http\Message\RequestInterface;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\League\Route\Router;
use DT\Plugin\Services\Renderer;
use DT\Plugin\Services\Template;
use function DT\Plugin\container;
use function DT\Plugin\namespace_string;
use function DT\Plugin\routes_path;

/**
 * Request middleware to be used in the request lifecycle.
 *
 * Class MiddlewareServiceProvider
 * @package DT\Plugin\Providers
 */
class RouteServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
    /**
     * Add any custom middleware that you would like to apply to every route
     * @var array
     */
    protected $middleware = [
        // CustomMiddleware::class,
	];

    protected $files = [
        [
            "file" => "api.php",
            'rewrite' => 'dt/plugin/api',
            'query' => 'dt-plugin-api',
        ],
        [
            "file" => "web.php",
            'rewrite' => 'dt/plugin',
            'query' => 'dt-plugin',
        ]
    ];

    /**
     * Get the files configuration.
     *
     * This method retrieves the files configuration by applying the 'route_files'
     * filter to the class property $files.
     *
     * @return array The files configuration.
     */
    protected function get_files() {
        return apply_filters( namespace_string( 'route_files' ), $this->files );
    }

    /**
     * Lazy load any services
     */
    public function register(): void
    {
        // We're using the boot method to eager load the router and middleware
    }

    /**
     * Eager load the router and load any routes
     */
    public function boot(): void
    {
        $this->getContainer()->add( StrategyInterface::class, function () {
            return new ApplicationStrategy();
        } )->addMethodCall( 'setContainer', [ $this->getContainer() ] );

        $this->getContainer()->add( \DT\Plugin\League\Route\Router::class, function () {
            return new Router();
        } )->addMethodCall( 'setStrategy', [ $this->getContainer()->get( StrategyInterface::class ) ] );

        $this->getContainer()->add( ServerRequestInterface::class, function () {
            return ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES // phpcs:ignore
            );
        } );

        $this->getContainer()->add( ResponseInterface::class, function () {
            return $this->getContainer()->get( Response::class );
        } );

        $router = $this->getContainer()->get( Router::class );

        foreach ( $this->middleware as $middleware ) {
            $router->middleware( $this->getContainer()->get( $middleware ) );
        }

        foreach ( $this->get_files() as $file ) {
            $this->process_file( $file );
        }
    }

    /**
     * Extracts and processes file information.
     *
     * @param array $file The array containing file configuration.
     * @return void
     * @throws \Exception When the file does not exist or the file rewrite requires a query variable.
     */
    public function process_file( $file ) {
        $defaults = [
            'file' => '',
            'rewrite' => '',
            'query' => '',
        ];
        $file = array_merge( $defaults, $file );
        $file_path = routes_path( $file['file'] );

        if ( !$file['file'] || ! file_exists( $file_path ) ) {
            if ( WP_DEBUG ) {
                throw new \Exception( esc_html( "The file $file_path does not exist." ) );
            } else {
                return;
            }
        }

        if ( $file['rewrite'] && ! $file['query'] ) {
            throw new \Exception( esc_html( "The file rewrite " . $file['rewrite'] . " must have a query var." ) );
        }

        if ( $file['query'] ) {
            add_filter( 'query_vars', function ( $vars ) use ( $file ) {
                return $this->file_query_vars( $file, $vars );
            }, 9, 1 );
        }

        if ( $file['rewrite'] ) {
            add_action( 'init', function () use ( $file ) {
                $this->file_rewrite_rules( $file );
            }, 9 );
            add_action( 'template_redirect', function () use ( $file ) {
                $this->file_template_redirect( $file );
            }, 1, 0 );
        } else {
            add_action( 'wp_loaded', function () use ( $file ) {
                $this->dispatch_file( $file );
            }, 20 );
        }
    }


    /**
     * Generate rewrite rules for a file.
     *
     * @param array $file The file configuration.
     * @return void
     */
    protected function file_rewrite_rules( $file ): void
    {
        add_rewrite_rule(
            '^' . $file['rewrite'] . '/?$',
            'index.php?' . $file['query'] .  '=/', 'top'
        );

        add_rewrite_rule(
            '^' . $file['rewrite'] . '/(.+)/?',
            'index.php?' . $file['query'] .  '=$matches[1]', 'top'
        );
    }

    /**
     * Add file query variable to the list of query variables.
     *
     * @param array $file The file configuration.
     * @param array $vars The list of query variables.
     * @return array The updated list of query variables.
     */
    public function file_query_vars( $file, $vars )
    {
        $vars[] = $file['query'];

        return $vars;
    }


    /**
     * Perform template redirect based on query var 'dt_autolink'.
     *
     * @return void
     */
    public function file_template_redirect( $file ): void {
        if ( ! get_query_var( $file['query'] ) ) {
            return;
        }

        if ( WP_DEBUG ) {
            $response = $this->dispatch_file( $file );
        } else {
            try {
                $response = $this->dispatch_file( $file );
            } catch ( NotFoundException $e ) {
                return;
            }
        }

        if ( !$response ) {
            return;
        }

       $this->render_response( $response );
    }

    /**
     * Renders the HTTP response.
     *
     * @param ResponseInterface $response The response to be rendered.
     * @return void
     * @throws \Exception When the file does not exist or the file rewrite requires a query variable.
     */
    public function render_response( ResponseInterface $response ) {
        $headers = $response->getHeaders();

        foreach ( $headers as $key => $value ) {
            header( $key . ': ' . $value[0] );
        }

        if ( $response->hasHeader( 'Content-Type' )
            && $response->getHeader( 'Content-Type' )[0] ?? false === 'application/json' ) {
            if ( $response->getStatusCode() !== 200 ) {
                wp_send_json_error( json_decode( $response->getBody() ), $response->getStatusCode() );
            }
            wp_send_json( json_decode( $response->getBody() ) );
            die();
        }

        if ( $response->getStatusCode() !== 200 ) {
            wp_die( esc_html( $response->getBody() ), esc_attr( $response->getStatusCode() ) );
        }

        $renderer = $this->getContainer()->get( Renderer::class );
        $renderer->render( $response );
    }

    /**
     * Dispatches a server request using a router.
     *
     * @param ServerRequestInterface $server_request The server request to be dispatched.
     * @return mixed The result of the router dispatch.
     */
    public function dispatch( ServerRequestInterface $server_request )
    {
        $router = container()->get( Router::class );
        return $router->dispatch( $server_request );
    }

    /**
     * Dispatches the file based on the given file configuration.
     *
     * @param array $file The array containing file configuration.
     * @return void|mixed Returns the result of the file dispatch if a query variable is provided, otherwise void.
     * @throws \Exception When the file does not exist or the file rewrite requires a query variable.
     */
    protected function dispatch_file( $file ) {
        $router = container()->get( Router::class );
        $r = $router;

        require_once routes_path( $file['file'] );


        if ( $file['query'] ) {
            $route = get_query_var( $file['query'] );

            if ( !$route ) {
                return false;
            }

            $route = '/' . trim( $route, '/' );

            return $router->dispatch( ServerRequestFactory::fromGlobals(
                array_merge( [], $_SERVER, [ 'REQUEST_URI' => $route ] ),
                $_GET, $_POST, $_COOKIE, $_FILES // phpcs:ignore
            ) );
        } else {
            return $router->dispatch( container()->get( ServerRequestInterface::class ) );
        }
    }


    /**
     * Check if the service provider provides a service.
     */
    public function provides( string $id ): bool
    {
        $services = [
            ServerRequestInterface::class,
            ResponseInterface::class,
            StrategyInterface::class,
            Router::class,
        ];

        return in_array( $id, $services );
    }
}
