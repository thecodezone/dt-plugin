<?php

namespace DT\Plugin\Services;

use DT\Plugin\Laminas\Diactoros\ServerRequest;
use DT\Plugin\Laminas\Diactoros\ServerRequestFactory;
use DT\Plugin\League\Route\Router;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\routes_path;

/**
 * Class Route
 *
 * Represents a route in the application.
 * @see https://route.thephpleague.com/4.x/usage/
 */
class Route implements RouteInterface
{
    /**
     * @var Router $router
     * The Router Instance.
     * @see https://route.thephpleague.com/4.x/usage/
     */
    protected $router;

    /**
     * The $request variable holds the HTTP request data.
     * @see https://www.php-fig.org/psr/psr-7/
     *
     * @var ServerRequestInterface | null
     */
    protected $request;

    /**
     * The $response variable holds the HTTP response data.
     * @see https://www.php-fig.org/psr/psr-7/
     *
     * @var ResponseInterface | null
     */
    protected $response;

    /**
     * The $renderer variable is used to generate HTML or JSON output from a Response.
     *
     * @var ResponseRenderer
     */
    protected $renderer;

    /**
     * Class constructor.
     *
     * @param Router $router The router instance.
     * @param ServerRequest $request The server request instance.
     * @param ResponseRenderer $renderer The response renderer instance.
     */
    public function __construct( Router $router, ServerRequestInterface $request, ResponseRenderer $renderer ) {
        $this->router = $router;
        $this->request = $request;
        $this->renderer = $renderer;
    }

    /**
     * Initialize the router with a specified URI instead of relying on the REQUEST_URI.
     * This is useful for testing or for scp[omg the router to a subdirectory.
     *
     * @param string $uri The URI to set for the request.
     * @return self The instance of the class.
     * @eee https://docs.laminas.dev/laminas-diactoros/
     */
    public function as_uri( $uri ): self {
        return $this->with_request(ServerRequestFactory::fromGlobals(
            array_merge( [], $_SERVER, [ 'REQUEST_URI' => $uri ] ),
            $_GET, $_POST, $_COOKIE, $_FILES // phpcs:ignore
        ));
    }

    /**
     * Adds middleware to the router.
     *
     * @param mixed $middleware The middleware to add. Can be a string or an array of strings.
     *
     * @return self The instance of the class.
     */
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


    /**
     * Sets the request for the route.
     *
     * @param mixed $request The request for the route.
     * @return self The instance of the class.
     */
    public function with_request( $request ): self {
        $this->request = $request;
        return $this;
    }

    /**
     * Adds routes to the router.
     *
     * @param callable $register_routes The callback function that registers routes to the router.
     * @return self The instance of the class.
     */
    public function with_routes( callable $register_routes ): self {
        $register_routes( $this->router );
        return $this;
    }

    /**
     * Loads routes from a route file located in the /routes directory.
     *
     * @param string $file The file path of the route file.
     * @return self Returns an instance of the current object.
     */
    public function from_route_file( $file ): self {
        return $this->from_file( routes_path( $file ) );
    }

    /**
     * Loads routes from a file and adds them to the route collector.
     *
     * @param string $file The path to the file containing the routes.
     * @return self Returns an instance of the current class.
     */
    public function from_file( $file ): self {
        return $this->with_routes( function ( $r ) use ( $file ) {
            require_once $file;
        });
    }

    /**
     * Dispatches the request to the router and sets the response.
     *
     * @return self Returns an instance of the current class.
     */
    public function dispatch(): self {
        $this->response = $this->router->dispatch( $this->request );
        return $this;
    }

    /**
     * Renders the response using the renderer if it exists.
     * If the response is not set, it dispatches the request to the router first.
     *
     * @return self Returns an instance of the current class.
     */
    public function render(): self {
        if ( !$this->response ) {
          $this->dispatch();
        }

        if ( $this->response ) {
            $this->renderer->render( $this->response );
        }

        return $this;
    }
}
