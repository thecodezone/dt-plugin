<?php
$error   = $error ?? false;
$content = $content ?? false;

if ( ! $error && ! $content ) {
	return;
}
?>
<sp-banner type="error">
	<?php if ( $error ): ?>
        <div slot="header"><?php echo esc_html( $error ); ?></div>
	<?php endif; ?>
	<?php if ( $content ): ?>
        <div slot="content"><?php echo esc_html( $content ); ?></div>
	<?php endif; ?>
</sp-banner>