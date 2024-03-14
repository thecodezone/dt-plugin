<?php
/**
 * Shortcode: Scripture
 *
 * @var array $contnet
 * @var array $attributes
 */

$this->layout( 'layouts/shortcode', [ 'error' => $error ?? false ] );
?>

<tbp-content content='<?php echo wp_json_encode( $content ); ?>'
             type="<?php echo esc_attr( $fileset_type ); ?>"
>
</tbp-content>