<?php
/**
 * Shortcode: Scripture
 *
 * @var array $content
 * @var array $attributes
 * @var string $reference
 * @var string $fileset_type
 */

$this->layout( 'layouts/shortcode', [ 'error' => $error ?? false ] );
?>
<tbp-content content='<?php echo wp_json_encode( $content ); ?>'
             reference='<?php echo wp_json_encode( $reference ); ?>'
             type="<?php echo esc_attr( $fileset_type ); ?>"
             heading_text="<?php if ( is_string( $attributes["heading_text"] ) ): ?><?php echo esc_attr( $attributes["heading_text"] ); ?><?php endif; ?>"
             <?php if ( $attributes["heading"] ): ?>heading<?php endif; ?>

>
</tbp-content>