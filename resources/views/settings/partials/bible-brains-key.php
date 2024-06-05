<?php

use function CodeZone\Bible\route_url;

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
                [
				      'fields'          => $fields,
				      'success_message' => __( 'Bible Brains API Key verified.', 'bible-plugin' ),
				      'redirect'        => esc_url( "/wp-admin/admin.php?page=bible-plugin" ),
				      'url'             => esc_url( '/wp-admin/admin.php?page=bible-plugin&tab=bible' ),
				      'action'          => esc_url( route_url( 'api/bible-brains/key' ) ),
				      'error'           => $error ?? '',
			      ]
            )
        )
    ); ?>)"
>

    <fieldset>

		<?php $this->insert( 'partials/alerts' ); ?>

        <div class="tbp-form-group">
            <sp-field-group>
                <sp-field-label
                        required
                        for="bible_brains_key"><?php esc_html_e( 'Bible Brain API Key', 'bible-plugin' ); ?></sp-field-label>

                <div>
                    <sp-textfield id="bible_brains_key"
                                  name="bible_brains_key"
                                  :value="dirty_bible_brains_key"
                                  :invalid="!verified"
                                  :valid="verified"
                                  @change="dirty_bible_brains_key = $event.target.value"
                                  placeholder="<?php esc_attr_e( 'Enter key...', 'bible-plugin' ); ?>"
                    ></sp-textfield>

                    <sp-button
                            x-show="!verified"
                            key="bible_brains_button_negative"
                            variant="negative"
                            label="<?php esc_attr_e( 'Validate', 'bible-plugin' ); ?>"
                            @click="submit"
                            size="m">
						<?php esc_html_e( 'Validate', 'bible-plugin' ); ?>
                        <sp-icon-key slot="icon"></sp-icon-key>
                    </sp-button>

                    <sp-button
                            x-show="verified"
                            key="bible_brains_button_positive"
                            variant="accent"
                            label="<?php esc_attr_e( 'Valid', 'bible-plugin' ); ?>"
                            @click="submit"
                            size="m">
						<?php esc_html_e( 'Valid', 'bible-plugin' ); ?>
                        <sp-icon-key slot="icon"></sp-icon-key>
                    </sp-button>
                </div>

                <sp-help-text size="s">
                    <sp-link href="https://4.dbt.io/api_key/request" target="_blank">
						<?php esc_html_e( "request a key", 'bible-plugin' ); ?>
                    </sp-link>
                </sp-help-text>
            </sp-field-group>
        </div>
    </fieldset>
</form>
