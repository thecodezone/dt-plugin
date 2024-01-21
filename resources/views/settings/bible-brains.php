<?php
/**
 * @var $error string
 * @var $success string
 * @var $nonce string
 * @var $tab string
 * @var $language_options array
 * @var $version_options array
 * @var $media_options array
 * @var $old array
 */
$this->layout( 'layouts/settings', compact( 'tab' ) )
?>

    <form method="post">
		<?php wp_nonce_field( 'dt_admin_form', 'bible_reader' ) ?>

        <fieldset>
            <div class="br-form-group">
                <sp-field-group>
                    <sp-field-label
                            required
                            for="bible_reader_bible_brains_key"><?php esc_html_e( 'Bible Brain API Key', 'bible-reader' ); ?></sp-field-label>

                    <div>
                        <sp-textfield id="bible_reader_bible_brains_key"
                                      name="bible_reader_bible_brains_key"
                                      value="<?php echo esc_attr( $old['bible_reader_bible_brains_key'] ?? null ); ?>"
                                      placeholder="<?php esc_attr_e( 'Enter key...', 'bible-reader' ); ?>"
                                      required
                        ></sp-textfield>
                        <sp-button variant="accent" quiet label="<?php esc_attr_e( 'Validate', 'bible-reader' ); ?>"
                                   size="m">
							<?php esc_html_e( 'Validate', 'bible-reader' ); ?>
                            <sp-icon-key slot="icon"></sp-icon-key>
                        </sp-button>
                    </div>

                    <sp-help-text size="s">
                        <sp-link href="https://scripture.api.bible/docs">
							<?php esc_html_e( "Here's how to get your key.", 'bible-reader' ); ?>
                        </sp-link>
                    </sp-help-text>
                </sp-field-group>
            </div>
        </fieldset>
    </form>

<?php if ( $old['bible_reader_bible_brains_key'] ): ?>
    <sp-divider size="s" x-show="Object.values(language_options).length > 0"></sp-divider>

    <form method="post"
        x-data="br_bible_brains_form(<?php echo esc_attr(
            wp_json_encode(
                array_merge(
                    $old,
                    [
					      'nonce'            => $nonce,
					      'language_options' => $language_options,
					      'version_options'  => $version_options,
					      'media_options'    => $media_options
				      ]
                )
            )
        ); ?>)"
          x-show="Object.values(language_options).length > 0"
          @submit="submit"
    >

        <fieldset>

            <br-alert-banner positive x-show:open="success" open x-ref="successAlert">
				<?php echo esc_html( $success ); ?>
            </br-alert-banner>


            <br-alert-banner negative x-show="!success & error" open x-ref="errorAlert">
				<?php echo esc_html( $error ); ?>
            </br-alert-banner>

            <sp-field-group>
                <sp-field-label
                        required
                        for="bible_reader_languages"><?php esc_html_e( 'Languages', 'bible-reader' ); ?></sp-field-label>

                <br-multi-picker id="bible_reader_languages"
                                 name="bible_reader_languages"
                                 label="Choose..."
                                 required
                                 :value="bible_reader_languages"
                                 @change="bible_reader_languages = $event.target.value"
                >
                    <template x-for="(label, value) in language_options" :key="value">
                        <sp-menu-item
                                :value="value">
                            <span x-text="label"></span>
                        </sp-menu-item>
                    </template>


                </br-multi-picker>

                <sp-help-text size="s">
					<?php esc_html_e( "Select the bible languages you would like to make available.", 'bible-reader' ); ?>
                </sp-help-text>
            </sp-field-group>

            <sp-field-group
                    x-show="bible_reader_languages && Object.values(selected_language_options).length > 0">
                <sp-field-label
                        required
                        for="bible_reader_language"
                ><?php esc_html_e( 'Default Language', 'bible-reader' ); ?></sp-field-label>

                <sp-picker id="bible_reader_language"
                           name="bible_reader_language"
                           label="Choose..."
                           required
                           :key="bible_reader_languages"
                           :value="bible_reader_language"
                           @change="bible_reader_language = $event.target.value">
                    <template x-for="(label, value) in language_options"
                              :key="value"
                    >
                        <sp-menu-item
                                x-show="bible_reader_languages.includes(value)"
                                :value="value">
                            <span x-text="label"></span>
                        </sp-menu-item>
                    </template>

                </sp-picker>

                <sp-help-text size="s">
					<?php esc_html_e( "Select the bible language that will be used by default.", 'bible-reader' ); ?>
                </sp-help-text>
            </sp-field-group>

            <sp-field-group x-show="bible_reader_languages && Object.values(version_options).length">
                <sp-field-label
                        required
                        for="default_version_options.length"><?php esc_html_e( 'Bible Versions', 'bible-reader' ); ?></sp-field-label>

                <br-multi-picker id="bible_reader_versions"
                                 name="bible_reader_versions"
                                 label="Choose..."
                                 required
                                 :value="bible_reader_versions"
                                 @change="
                                    bible_reader_versions = $event.target.value;
                                 ">
                    <template
                            x-for="(label, value) in version_options"
                            :key="value"
                    >
                        <sp-menu-item :value="value">
                            <span x-text="label"></span>
                        </sp-menu-item>
                    </template>
                </br-multi-picker>
                <sp-help-text size="s">
					<?php esc_html_e( "Select the bible versions you would like to make available.", 'bible-reader' ); ?>
                </sp-help-text>
            </sp-field-group>

            <sp-field-group x-show="bible_reader_versions && Object.values(selected_version_options).length">
                <sp-field-label
                        required
                        for="bible_reader_version"
                ><?php esc_html_e( 'Default Bible Version', 'bible-reader' ); ?></sp-field-label>

                <sp-picker id="bible_reader_version"
                           name="bible_reader_version"
                           label="Choose..."
                           required
                           :value="bible_reader_version"
                           @change="bible_reader_version = $event.target.value"
                >
                    <template x-for="(label, value) in version_options"
                              :key="value">
                        <sp-menu-item
                                x-show="bible_reader_versions.includes(value)"
                                :value="value">
                            <span x-text="label"></span>
                        </sp-menu-item>
                    </template>

                </sp-picker>

                <sp-help-text size="s">
					<?php esc_html_e( "Select the bible version that will be used by default.", 'bible-reader' ); ?>
                </sp-help-text>
            </sp-field-group>

            <sp-field-group x-show="bible_reader_versions && Object.values(media_options).length">
                <sp-field-label
                        required
                        for="bible_reader_media"
                >
					<?php esc_html_e( 'Media Types', 'bible-reader' ); ?>
                </sp-field-label>

                <input type="hidden" name="bible_reader_media" x-model="bible_reader_media">

                <sp-field-group horizontal>
                    <template x-for="(label, value) in media_options">
                        <sp-checkbox required
                                     size="m"
                                     @change="$stringable_checkbox_change('bible_reader_media', $event)"
                                     :checked="$as_array('bible_reader_media').includes(value)"
                                     :value="value"
                        >
                            <span x-text="label"></span>
                        </sp-checkbox>
                    </template>

                </sp-field-group>


                <sp-help-text size="s">
					<?php esc_html_e( "Note that some bible versions do not support all media types.", 'bible-reader' ); ?>
                </sp-help-text>
            </sp-field-group>

            <sp-button-group>
                <sp-button
                        type="submit"
                        variant="accent"
                        label="Save"
                >
					<?php esc_html_e( 'Save', 'bible-reader' ); ?>
                </sp-button>
            </sp-button-group>
        </fieldset>
    </form>
<?php endif; ?>