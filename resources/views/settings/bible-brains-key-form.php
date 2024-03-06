<?php
/**
 * @var string $nonce
 * @var string $fields
 * @var string $tab
 * @var string $error
 */
$this->layout( 'layouts/settings', compact( 'tab' ) );

$this->insert( 'settings/partials/bible-brains-key', compact( 'nonce', 'fields', 'error' ) )
?>
