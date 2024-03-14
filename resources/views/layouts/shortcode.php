<?php

use function CodeZone\Bible\get_plugin_option;

$color_scheme = get_plugin_option( 'color_scheme' );
$colors       = get_plugin_option( 'colors' );
$accent_steps = $colors['accent_steps'] ?? [];
$error        = $error ?? false;
?>

<sp-theme scale="medium"
          class="tbp-cloak"
          color="<?php echo esc_attr( $color_scheme ); ?>"
          style="<?php if ( ! empty( $accent_steps['600'] ) ): ?>
                  --spectrum-accent-color-default: <?php echo esc_attr( $accent_steps['600'] ?? '' ) ?>;
          <?php endif; ?>
          <?php foreach ( $accent_steps as $step => $rgba ): ?>
                  --spectrum-accent-color-<?php echo esc_attr( $step ) ?>: <?php echo esc_attr( $rgba ) ?>;
          <?php endforeach; ?>">
    <div class="tbp__shortcode">
		<?php if ( $error ): ?>
            <div class="tpb__shortcode__error">
				<?php $this->insert( 'partials/error', [ 'error' => $error ] ) ?>
            </div>
		<?php else : ?>
			<?php echo $this->section( 'content' ) ?>
		<?php endif; ?>
    </div>
</sp-theme>
