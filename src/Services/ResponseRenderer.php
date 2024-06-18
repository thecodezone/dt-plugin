<?php

namespace DT\Plugin\Services;

use DT\Plugin\Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseRenderer
 *
 * This class is responsible for rendering the response based on the provided ResponseInterface object.
 * @see https://www.php-fig.org/psr/psr-7/
 */
class ResponseRenderer implements ResponseRendererInterface
{
    /**
     * Render method
     *
     * @param ResponseInterface $response The response object containing the headers and body to be rendered
     *
     * @return void
     * @see https://www.php-fig.org/psr/psr-7/
     */
    public function render( ResponseInterface $response ) {
        $headers = $response->getHeaders();

        foreach ( $headers as $key => $value ) {
            header( $key . ': ' . $value[0] );
        }

        $code_type = $this->guess_code_type( $response );

        switch ( $code_type ) {
            case 'redirect':
                $this->render_redirect( $response );
                break;
            case 'error':
                $this->render_error( $response );
                break;
            default:
                $this->render_success( $response );
                break;
        }
    }

    /**
     * Redirects the user to the specified location.
     *
     * @param ResponseInterface $response The response object.
     * @return void
     */
    protected function render_redirect( ResponseInterface $response ) {
        wp_redirect( $response->getHeader( 'Location' )[0] );
        die();
    }

    /**
     * Renders a successful response.
     *
     * @param ResponseInterface $response The response object.
     * @return void
     */
    protected function render_success( ResponseInterface $response ) {
        $is_json = $this->is_json( $response );

        if ( $is_json ) {
            wp_send_json( json_decode( $response->getBody() ) );
            die();
        } elseif ( apply_filters( 'dt_blank_access', false ) ) {
                add_action( 'dt_blank_body', function () use ( $response ) {
                    // phpcs:ignore
                    echo $response->getBody();
                }, 9 );

                $path = get_theme_file_path( 'template-blank.php' );
                include $path;
                die();
		} else {
			echo $response->getBody();
			die();
        }
    }

    /**
     * Renders an error response based on the provided HTTP response.
     *
     * @param ResponseInterface $response The response object.
     * @return void
     */
    protected function render_error( ResponseInterface $response ) {
        $is_json = $this->is_json( $response );

        if ( $is_json ) {
            wp_send_json_error( json_decode( $response->getBody() ), $response->getStatusCode() );
            die();
        } else {
            wp_die( esc_html( $response->getBody() ), esc_attr( $response->getStatusCode() ) );
        }
    }

    /**
     * Determines the type of code based on the HTTP response code.
     *
     * @param ResponseInterface $response The response object.
     * @return string The code type. Possible values are:
     *     - success: If the response code is between 200 and 299 (inclusive).
     *     - redirect: If the response code is between 300 and 399 (inclusive).
     *     - error: If the response code is between 400 and 499 (inclusive), or is 500 or greater.
     */
    protected function guess_code_type( ResponseInterface $response ) {
        $code = $response->getStatusCode();
        if ( $code >= 200 && $code < 300 ) {
            return 'success';
        }
        if ( $code >= 300 && $code < 400 ) {
            return 'redirect';
        }
        if ( $code >= 400 && $code < 500 ) {
            return 'error';
        }
        if ( $code >= 500 ) {
            return 'error';
        }
    }

    /**
     * Checks if the given response is in JSON format.
     *
     * @param ResponseInterface $response The response object to check.
     *
     * @return bool Returns true if the response is in JSON format, false otherwise.
     */
    protected function is_json( ResponseInterface $response ) {
        return $response->hasHeader( 'Content-Type' ) && $response->getHeader( 'Content-Type' )[0] ?? false === 'application/json';
    }
}
