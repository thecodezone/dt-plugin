<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Laminas\Diactoros\Response;
use DT\Plugin\Laminas\Diactoros\ServerRequestFactory;
use DT\Plugin\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Plugin\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Plugin\League\Route\Http\Exception\NotFoundException;
use DT\Plugin\League\Route\Strategy\ApplicationStrategy;
use DT\Plugin\League\Route\Strategy\StrategyInterface;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\League\Route\Router;
use DT\Plugin\Services\ResponseRenderer;
use DT\Plugin\Services\Route;
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

        $this->getContainer()->add( Router::class, function () {
            return $this->getContainer()->get( Router::class );
        } );

        $this->getContainer()->add( Route::class, function () {
            return new Route(
                $this->getContainer()->get( Router::class ),
                $this->getContainer()->get( ServerRequestInterface::class ),
                $this->getContainer()->get( ResponseRenderer::class ) );
        } );

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

        $uri = '/' . trim( get_query_var( $file['query'] ) , '/' );

        $route = $this->getContainer()->get( Route::class );
        $route->with_middleware( $this->middleware )
            ->from_route_file( $file['file'] )
            ->as_uri( $uri );

        if ( WP_DEBUG ) {
            $route->dispatch();
        } else {
            try {
                $route->dispatch();
            } catch ( NotFoundException $e ) {
                return;
            }
        }

       $route->render();
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
            Route::class
        ];

        return in_array( $id, $services );
    }
}
