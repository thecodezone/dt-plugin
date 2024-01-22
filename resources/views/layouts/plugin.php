<header>
    <h1><?php esc_html_e( 'Plugin', 'bible-plugin' ); ?></h1>
</header>

<div>
	<?php echo $this->section( 'content' ) ?>
</div>

<footer>
    <p>
		<?php esc_html_e( 'Copyright ', 'bible-plugin' ); ?>

		<?php echo $this->e( gmdate( 'Y' ) ); ?>
    </p>
</footer>