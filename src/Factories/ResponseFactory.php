<?php

namespace DT\Plugin\Factories;

use DT\Plugin\GuzzleHttp\Psr7\Response;
use DT\Plugin\Psr\Http\Message\ResponseInterface;
use function DT\Plugin\container;

/**
 * The ResponseFactory class is responsible for creating and redirecting HTTP responses.
 */
class ResponseFactory {
    /**
     * Redirects the user to the specified URL.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP status code to use for the redirect. Default is 302 (Found).
     * @param array $headers Additional headers to send with the redirect request. Default is an empty array.
     * @return ResponseInterface A new instance of the Response class.
     */
    public static function redirect( string $url, int $status = 302, array $headers = [] ): ResponseInterface {
        return static::make( '', $status, array_merge( $headers, [ 'Location' => $url ] ) );
    }

    /**
     * Creates a new instance of the Response class.
     *
     * @return ResponseInterface A new instance of the Response class.
     */
    public static function make( $content = null, int $status = 200, array $headers = [] ): ResponseInterface {
        if ( is_array( $content ) ) {
            $content = json_encode( $content );
            $headers['Content-Type'] = 'application/json';
        }
        $response = container()->get( ResponseInterface::class );
        $response->getBody()->write( $content );
        $response = $response->withStatus( $status );
        foreach ( $headers as $key => $value ) {
            $response = $response->withHeader( $key, $value );
        }
        return $response;
    }
}
