<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Factories\ResponseFactory;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use DT\Plugin\Psr\Http\Server\MiddlewareInterface;
use DT\Plugin\Psr\Http\Server\RequestHandlerInterface;
use function DT\Plugin\redirect;

/**
 * Class HasCap
 *
 * Implements the Middleware interface and checks if a user has sufficient permissions.
 */
class HasCap implements MiddlewareInterface
{
    /**
     * @var string|iterable $capabilities
     * The variable that holds the capabilities of the system.
     * It can either be a string or an iterable data structure.
     */
    public iterable $capabilities = [];

    /**
     * @var string|null $redirect_to
     * The variable that holds the URL to redirect to.
     * It can either be a string representing a valid URL or null if no redirection is needed.
     */
    protected $redirect_to;

    /**
     * __construct method.
     *
     * Constructs a new instance of the class.
     *
     * @param string|iterable $capabilities The capabilities parameter that determines the
     *                                      permissions or features associated with the object being constructed.
     * @param string|null $redirect_to The redirect_to parameter that specifies the target URL to redirect to after
     *                                completing the construction. It is set to null by default.
     */
    public function __construct( $capabilities, $redirect_to = null )
    {
        $this->capabilities = is_string( $capabilities ) ? explode( ',', $capabilities ) : $capabilities;
        $this->redirect_to  = $redirect_to;
    }

    /**
     * process method.
     *
     * Processes the server request by checking user capabilities and handling insufficient permissions.
     *
     * @param ServerRequestInterface $request The server request object containing the request parameters and data.
     * @param RequestHandlerInterface $handler The request handler that will handle the request.
     *
     * @return ResponseInterface The response generated after processing the server request.
     */
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        $user = wp_get_current_user();

        if ( ! $user ) {
            return $this->handleInsufficientPermissions( $request );
        }

        foreach ( $this->capabilities as $capability ) {
            $can = $user->has_cap( $capability );

            if ( !$can ) {
                return $this->handleInsufficientPermissions( $request );
            }
        }

        return $handler->handle( $request );
    }

    /**
     * handleInsufficientPermissions method.
     *
     * Handles the case when the user has insufficient permissions to access a specific resource.
     *
     * @param ServerRequestInterface $request The request object representing the incoming HTTP request.
     *
     * @return ResponseInterface The response object after handling the insufficient permissions.
     *                  It can either be a redirect response or an abort response.
     */
    protected function handleInsufficientPermissions( ServerRequestInterface $request ): ResponseInterface
    {
        if ( $this->redirect_to ) {
           return redirect( $this->redirect_to );
        } else {
            return $this->abort();
        }
    }

    /**
     * abort method.
     *
     * Aborts the current HTTP request and returns a response with a 403 status code and an error message.
     **
     * @return ResponseInterface The response object with a 403 status code and the error message set as its content.
     */
    protected function abort(): ResponseInterface
    {
        return ResponseFactory::make( $this->getErrorMessage(), 403 );
    }

    /**
     * getErrorMessage method.
     *
     * Retrieves the error message indicating insufficient access.
     *
     * @return string|null The error message indicating insufficient access.
     *                     Returns null if the message could not be retrieved.
     */
    protected function getErrorMessage(): ?string
    {
        return __( 'You do not have sufficient access.', 'dt-plugin' );
    }
}
