<?php
$this->layout( 'layouts/plugin' );
?>

<div>
	<b>
		Subpage
	</b>
</div>

<a href="<?php echo esc_attr( $home_url ); ?>">
	<?php esc_html_e( 'Visit home', 'dt-plugin' ); ?>
</a>
