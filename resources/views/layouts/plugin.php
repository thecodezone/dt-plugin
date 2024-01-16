<header>
    <h1><?php esc_html_e( 'Plugin', 'bible-reader' ); ?></h1>
</header>

<div>
	<?php echo $this->section( 'content' ) ?>
</div>

<footer>
    <p>
		<?php esc_html_e( 'Copyright ', 'bible-reader' ); ?>

		<?php echo $this->e( gmdate( 'Y' ) ); ?>
    </p>
</footer>