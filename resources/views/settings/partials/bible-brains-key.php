<?php
/**
 * Bible Brains Key Form
 *
 * @var string $success
 * @var string $error
 * @var string $nonce
 * @var string $redirect_url
 * @var array $fields
 */
?>
<form method="post"
    x-data="br_bible_brains_key_form(<?php echo esc_attr(
        wp_json_encode(
            array_merge(
                $fields,
                [
				      'success_message' => __( 'Bible Brains API Key verified.', 'bible-plugin' ),
				      'nonce'           => $nonce,
				      'redirect_url'    => esc_url( "/wp-admin/admin.php?page=bible-plugin" ),
				      'url'             => esc_url( '/wp-admin/admin.php?page=bible-plugin&tab=bible' ),
				      'action'          => esc_url( '/bible/api/bible-brains/key' ),
			      ]
            )
        )
    ); ?>)"
      @submit="submit"
>

    <fieldset>

		<?php $this->insert( 'partials/alerts' ); ?>

        <div class="br-form-group">
            <sp-field-group>
                <sp-field-label
                        required
                        for="bible_plugin_bible_brains_key"><?php esc_html_e( 'Bible Brain API Key', 'bible-plugin' ); ?></sp-field-label>

                <div>
                    <sp-textfield id="bible_plugin_bible_brains_key"
                                  name="bible_plugin_bible_brains_key"
                                  :value="dirty_bible_plugin_bible_brains_key"
                                  :invalid="!verified"
                                  :valid="verified"
                                  @change="dirty_bible_plugin_bible_brains_key = $event.target.value"
                                  placeholder="<?php esc_attr_e( 'Enter key...', 'bible-plugin' ); ?>"
                    ></sp-textfield>
                    <sp-button
                            x-show="!verified"
                            key="bible_plugin_bible_brains_button_negative"
                            variant="negative"
                            label="<?php esc_attr_e( 'Validate', 'bible-plugin' ); ?>"
                            @click="validate_bible_brains_key"
                            size="m">
						<?php esc_html_e( 'Validate', 'bible-plugin' ); ?>
                        <sp-icon-key slot="icon"></sp-icon-key>
                    </sp-button>
                    <sp-button
                            x-show="verified"
                            key="bible_plugin_bible_brains_button_positive"
                            variant="accent"
                            label="<?php esc_attr_e( 'Valid', 'bible-plugin' ); ?>"
                            @click="validate"
                            size="m">
						<?php esc_html_e( 'Valid', 'bible-plugin' ); ?>
                        <sp-icon-key slot="icon"></sp-icon-key>
                    </sp-button>
                </div>

                <sp-help-text size="s">
                    <sp-link href="https://scripture.api.bible/docs">
						<?php esc_html_e( "Here's how to get your key.", 'bible-plugin' ); ?>
                    </sp-link>
                </sp-help-text>
            </sp-field-group>
        </div>
    </fieldset>
</form>
