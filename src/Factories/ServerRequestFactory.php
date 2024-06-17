<?php

namespace DT\Plugin\Factories;

use DT\Plugin\GuzzleHttp\Psr7\CachingStream;
use DT\Plugin\GuzzleHttp\Psr7\LazyOpenStream;
use DT\Plugin\GuzzleHttp\Psr7\ServerRequest;
use DT\Plugin\Psr\Http\Message\ServerRequestInterface;

/**
 * Class ContainerFactory
 *
 * The ContainerFactory class is responsible for creating and managing instances of the Container class.
 *
 * @see https://container.thephpleague.com/4.x/
 */
class ServerRequestFactory {
    /**
     * Sets the REQUEST_URI value in the server parameters and returns a new instance of the object.
     *
     * @param string $uri The new value for REQUEST_URI.
     * @return ServerRequestInterface A new instance of the object with the updated REQUEST_URI value.
     */
    public static function with_uri( $uri ): ServerRequestInterface {
        return self::make( array_merge( $_SERVER, [
            'REQUEST_URI' => $uri
        ]), $_GET, $_POST, $_COOKIE, $_FILES ); // phpcs:ignore
    }

    /**
     * Creates a new instance of the ServerRequestInterface from the global variables.
     *
     * @return ServerRequestInterface The created ServerRequestInterface instance
     */
    public static function from_globals(): ServerRequestInterface {
        return self::make( $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES ); // phpcs:ignore
    }

    /**
     * Creates a new instance of the ServerRequestInterface from the given parameters.
     *
     * @param array $server The server parameters (from $_SERVER)
     * @param array $get The GET parameters (from $_GET)
     * @param array $post The POST parameters (from $_POST)
     * @param array $cookie The COOKIE parameters (from $_COOKIE)
     * @param array $files The uploaded files parameters (from $_FILES)
     * @return ServerRequestInterface The created ServerRequestInterface instance
     */
    public static function make( $server, $get, $post, $cookie, $files ): ServerRequestInterface {
        $method = $server['REQUEST_METHOD'] ?? 'GET';
        $headers = \getallheaders();
        $uri = $server['REQUEST_URI'] ?? ServerRequest::getUriFromGlobals();
        $body = new CachingStream( new LazyOpenStream( 'php://input', 'r+' ) );
        $protocol = isset( $server['SERVER_PROTOCOL'] ) ? \str_replace( 'HTTP/', '', $server['SERVER_PROTOCOL'] ) : '1.1';
        $server_request = new ServerRequest( $method, $uri, $headers, $body, $protocol, $server );
        return $server_request->withCookieParams( $cookie )
            ->withQueryParams( $get )
            ->withParsedBody( $post )
            ->withUploadedFiles( ServerRequest::normalizeFiles( $files ) );
    }
}
