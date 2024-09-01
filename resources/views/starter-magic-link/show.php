<?php
/**
 * @var WP_User $user
 * @var string $subpage_url
 */
?>
<?php $this->layout( 'layouts/plugin' ); ?>

<div>
    <b>
        Name: <?php echo esc_html( $user->user_nicename ); ?>
    </b>
</div>

<a href="<?php echo esc_url( $subpage_url ); ?>">
    <?php esc_html_e( 'Visit subpage', 'dt-plugin' ); ?>
</a>
