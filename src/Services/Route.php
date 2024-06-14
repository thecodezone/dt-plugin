<?php

namespace DT\Plugin\Services;

use DT\Plugin\Laminas\Diactoros\ServerRequest;
use DT\Plugin\Laminas\Diactoros\ServerRequestFactory;
use DT\Plugin\League\Route\Router;
use function DT\Plugin\routes_path;

/**
 * Class Route
 *
 * Represents a route in the application.
 */
class Route {
    protected $router;
    protected $request;
    protected $response;
    protected $renderer;

    public function __construct( Router $router, ServerRequest $request, ResponseRenderer $renderer) {
        $this->router = $router;
        $this->request = $request;
        $this->renderer = $renderer;
    }

    /**
     * Sets the request URI for the object.
     *
     * @param string $uri The URI to set for the request.
     *
     * @return void
     */
    public function as_uri( $uri ): self {
        return $this->with_request(ServerRequestFactory::fromGlobals(
            array_merge( [], $_SERVER, [ 'REQUEST_URI' => $uri ] ),
            $_GET, $_POST, $_COOKIE, $_FILES // phpcs:ignore
        ));
    }

    public function with_middleware( $middleware ): self {

        if ( is_array( $middleware ) ) {
            foreach ( $middleware as $m ) {
                $this->router->middleware( $m );
            }
            return $this;
        } else {
            $this->router->middleware( $middleware );
        }

        return $this;
    }


    public function with_request( $request ): self {
        $this->request = $request;
        return $this;
    }

    public function with_routes(callable $register_routes ): self {
        $register_routes( $this->router );
        return $this;
    }

    public function from_route_file( $file ): self {
        return $this->from_file( routes_path( $file ) );
    }

    public function from_file( $file ): self {
        return $this->with_routes( function ( $r ) use ( $file ) {
            require_once $file;
        });
    }

    /**
     * Dispatches the route.
     *
     * @return mixed The response from the route.
     */
    public function dispatch() {
        $this->response = $this->router->dispatch( $this->request );
        return $this;
    }

    public function render(): self {
        if ( $this->response ) {
            $this->renderer->render( $this->response );
        }
        return $this;
    }
}
