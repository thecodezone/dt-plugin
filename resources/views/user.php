<?php
/**
 * @var WP_User $user
 */
?>
<?php $this->layout( 'layouts/plugin' ); ?>

<div>
    <b>
        Logged in as <?php echo esc_html( $user->user_nicename ) ?>!
    </b>
</div>
