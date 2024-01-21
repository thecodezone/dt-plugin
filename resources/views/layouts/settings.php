<?php
/**
 * @var $tab string
 **/

/**
 * Variable representing the navigation menu.
 *
 * @var array $nav
 */
$nav = apply_filters( 'bible_reader_settings_tabs', [] );

/**
 * Is there a Bible Brain API Key?
 *
 * @var bool $has_api_key
 */
$has_api_key = ! ! get_option( 'bible_reader_bible_brains_key', false );
?>
<sp-theme scale="medium" color="lightest" theme="spectrum">
    <div class="bible-reader br-cloak wrap">
        <h2><?php esc_html_e( 'Bible Reader', 'bible-reader' ) ?></h2>

		<?php if ( ! $has_api_key ): ?>
            <sp-toast variant="negative" size="s" float open>
                You must add your Bible Brain API Key in order for this plugin to work.

                <a href="https://scripture.api.bible/docs" slot="action">
                    <sp-button
                            static="white"
                            variant="secondary"
                            treatment="outline"
                            size="s"
                    >
						<?php esc_html_e( "More Info", 'bible-reader' ); ?>
                    </sp-button>
                </a>
            </sp-toast>
		<?php endif; ?>

        <sp-divider size="l"></sp-divider>

        <sp-tabs size="l" emphasized selected="<?php echo esc_attr( $tab ) ?>">
			<?php foreach ( $nav as $index => $item ): ?>
                <sp-tab label="<?php echo esc_html( $item['label'] ) ?>"
                        href="<?php echo esc_url( admin_url( 'admin.php?page=bible-reader&tab=' . $item['tab'] ) ) ?>"
                        value="<?php echo esc_attr( $item['tab'] ) ?>"
                        onclick="window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=bible-reader&tab=' . $item['tab'] ) ) ?>'"
                ></sp-tab>
			<?php endforeach; ?>
        </sp-tabs>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
					<?php echo $this->section( 'content' ) ?>
                </div>
            </div>
        </div>

        <div id="postbox-container-1" class="postbox-container">
			<?php echo $this->section( 'right' ) ?>
        </div>
</sp-theme>