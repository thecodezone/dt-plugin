<?php

namespace DT\Plugin\Controllers;

use DT\Plugin\Psr\Http\Message\ServerRequestInterface;
use function DT\Plugin\template;
use function DT\Plugin\response;

/**
 * And example controller class.
 * Controllers are classes that are responsible for handling requests and returning responses.
 * Response objects can be modified by the controller methods, or you can return a string or array
 * from the method, and it will be automatically added to the response object.
 *
 * @package Controllers
 */
class HelloController {
    /**
     * Sets the content of the response to a success message and returns the response object.
     *
     * @param ServerRequestInterface $request The request object.
     */
    public function data( ServerRequestInterface $request ) {
        return response( [
            'message' => 'Hello, World!'
        ] );
    }

    /**
     * You can also return a string or array from a controller method,
     * it will be automatically added to the response object.
     *
     * @param ServerRequestInterface $request The request object.
     */
    public function show( ServerRequestInterface $request ) {
        return template( 'hello', [
            'name' => 'Friend'
        ] );
    }
}
