<?php
/**
 * Bible Brains Setup Form
 *
 * @var $action string
 * @var $nonce string
 * @var $tab string
 * @var $language_options array
 * @var $bible_options array
 * @var $media_type_options array
 * @var $fields array
 * @var $key_action string
 * @var $language_options_endpoint string
 * @var $bible_options_endpoint string
 * @var $search_label string
 */
$this->layout( 'layouts/settings', compact( 'tab' ) );

$this->insert( 'settings/partials/bible-brains-key', [
	'nonce'  => $nonce,
	'fields' => $fields
] );
?>

<form method="post"
      id="bible-brains-form"
      @submit="submit"
    x-data="br_bible_brains_form(<?php echo esc_attr(
        wp_json_encode(
            [
			      'fields'                    => $fields,
			      'action'                    => $action,
			      'nonce'                     => $nonce,
			      'bible_options'             => $bible_options,
			      'media_type_options'        => $media_type_options,
			      'language_options'          => $language_options,
			      'language_options_endpoint' => $language_options_endpoint,
			      'bible_options_endpoint'    => $bible_options_endpoint,
		      ]
        )
    ); ?>)"
>

    <sp-divider size="s"></sp-divider>

    <fieldset>

        <sp-field-group>

            <sp-field-label
                    require
                    for="bible_plugin_languages"><?php esc_html_e( 'Languages', 'bible-plugin' ); ?></sp-field-label>

            <tbp-overlay-picker id="languages"
                                name="languages"
                                placeholder="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>..."
                                label="<?php esc_attr_e( 'Languages', 'bible-plugin' ); ?>"
                                searchLabel="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>"
                                :value="fields.languages"
                                @change="fields.languages = $event.target.value;"
                                @options="language_options = Object.values($event.target.option_history)"
                                :optionsUrl="language_options_endpoint"
                                options="<?php echo esc_attr( wp_json_encode( $language_options ) ); ?>"
                                required
                                searchable
            >
            </tbp-overlay-picker>

            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible languages you would like to make available.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group
                x-show="fields.languages && selected_language_options">
            <sp-field-label
                    required
                    for="language"
            ><?php esc_html_e( 'Default Language', 'bible-plugin' ); ?></sp-field-label>

            <sp-picker
                    id="language"
                    name="language"
                    label="<?php esc_attr_e( 'Choose', 'bible-plugin' ); ?>..."
                    :value="fields.language"
                    autocomplete
                    grows
                    required
                    @change="fields.language = $event.target.value"
            >
                <template x-for="({itemText, value}) in selected_language_options"
                          :key="value"
                >
                    <sp-menu-item
                            :value="value">
                        <span x-text="itemText"></span>
                    </sp-menu-item>
                </template>
            </sp-picker>

            <sp-help-text size="s">
				<?php esc_html_e( "Select   the bible language that will be used by default.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group x-show="fields.languages && bible_options.length">
            <sp-field-label
                    required
                    for="default_bible"><?php esc_html_e( 'Bible Versions', 'bible-plugin' ); ?></sp-field-label>

            <tbp-overlay-picker
                    id="bibles"
                    name="bibles"
                    placeholder="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>..."
                    label="<?php esc_attr_e( 'Translations', 'bible-plugin' ); ?>"
                    searchLabel="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>"
                    :value="fields.bibles"
                    @change="fields.bibles = $event.target.value"
                    @options="bible_options = Object.values($event.target.option_history)"
                    :nonce="nonce"
                    :options="JSON.stringify(bible_options)"
                    :optionsUrl="language_bibles_options_endpoint"
                    prefetch
                    searchFetch
                    required
            >
            </tbp-overlay-picker>
            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible versions you would like to make available.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group x-show="fields.bibles && Object.values(media_type_options).length">
            <sp-field-label
                    required
                    for="media_types"
            >
				<?php esc_html_e( 'Media Types', 'bible-plugin' ); ?>
            </sp-field-label>

            <input type="hidden" name="media_types" x-model="fields.media_types">

            <sp-field-group horizontal>
                <template x-for="({itemText, value}) in media_type_options">
                    <sp-checkbox required
                                 size="m"
                                 name="media_types_checkbox"
                                 @change="$stringable_checkbox_change('fields.media_types', $event)"
                                 :checked="$as_array('fields.media_types').includes(value)"
                                 :value="value"
                    >
                        <span x-text="itemText"></span>
                    </sp-checkbox>
                </template>

            </sp-field-group>


            <sp-help-text size="s">
				<?php esc_html_e( "Note that some bible versions do not support all media types.", 'bible-plugin' ); ?>
            </sp-help-text>

        </sp-field-group>

		<?php $this->insert( 'partials/alerts' ); ?>

        <sp-button-group>

            <sp-button
                    active="false"
                    type="submit"
                    variant="accent"
                    label="<?php esc_attr_e( 'Save', 'bible-plugin' ); ?>"
            >
				<?php esc_html_e( 'Save', 'bible-plugin' ); ?>
            </sp-button>
        </sp-button-group>
    </fieldset>
</form>
