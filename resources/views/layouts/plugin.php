<header>
    <h1><?php esc_html_e( 'Plugin', 'dt_plugin' ); ?></h1>
</header>

<div>
    <?php //@phpcs:ignore
    echo $this->section( 'content' )
    ?>
</div>

<footer>
    <p>
        <?php esc_html_e( 'Copyright ', 'dt_plugin' ); ?>

        <?php echo esc_html( gmdate( 'Y' ) ); ?>
    </p>
</footer>
