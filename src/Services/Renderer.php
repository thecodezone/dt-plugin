<?php

namespace DT\Plugin\Services;

use DT\Plugin\Psr\Http\Message\ResponseInterface;

class Renderer {
    public function render( ResponseInterface $response ) {
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


        if ( apply_filters( 'dt_blank_access', false ) ) {
            add_action( 'dt_blank_body', function () use ( $response ) {
                // phpcs:ignore
                echo $response->getBody();
                die();
            }, 9 );

            $path = get_theme_file_path( 'template-blank.php' );
            include $path;
            die();
        } else {
            echo $response->getBody();
            die();
        }
    }
}
