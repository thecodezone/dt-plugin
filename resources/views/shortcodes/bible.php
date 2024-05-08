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

<tbp-bible
        reference="<?php echo $attributes['reference']; ?>"
></tbp-bible>