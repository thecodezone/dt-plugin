<?php
$this->layout( 'layouts/settings', compact( 'tab' ) )
?>

    <form method="post">
		<?php wp_nonce_field( 'dt_admin_form', 'dt_admin_form_nonce' ) ?>

        <sp-button onclick="alert('I was clicked');">Click me!</sp-button>
        <!-- Add a form -->
    </form>

<?php $this->start( 'right' ) ?>

    <!-- Add some content to the right side -->

<?php $this->stop() ?>