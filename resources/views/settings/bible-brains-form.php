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

            <br-picker id="bible_plugin_languages"
                       name="bible_plugin_languages"
                       placeholder="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>..."
                       required
                       :value="fields.bible_plugin_languages"
                       @change="fields.bible_plugin_languages = $event.target.value"
                       @options="language_options = Object.values($event.target.option_history)"
                       :nonce="nonce"
                       :optionsUrl="language_options_endpoint"
                       options="<?php echo esc_attr( wp_json_encode( $language_options ) ); ?>"
                       multiple
            >
            </br-picker>

            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible languages you would like to make available.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group
                x-show="fields.bible_plugin_languages && selected_language_options">
            <sp-field-label
                    required
                    for="bible_plugin_language"
            ><?php esc_html_e( 'Default Language', 'bible-plugin' ); ?></sp-field-label>

            <br-picker id="bible_plugin_language"
                       name="bible_plugin_language"
                       placeholder="<?php esc_attr_e( 'Choose', 'bible-plugin' ); ?>..."
                       required
                       :value="fields.bible_plugin_language"
                       @change="fields.bible_plugin_language = $event.target.value"
                       :options="JSON.stringify(selected_language_options)">
            </br-picker>

            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible language that will be used by default.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group x-show="fields.bible_plugin_languages && bible_options.length">
            <sp-field-label
                    required
                    for="default_bible"><?php esc_html_e( 'Bible Versions', 'bible-plugin' ); ?></sp-field-label>

            <br-picker id="bible_plugin_bibles"
                       name="bible_plugin_bibles"
                       placeholder="<?php esc_attr_e( 'Search', 'bible-plugin' ); ?>..."
                       :value="fields.bible_plugin_bibles"
                       @change="fields.bible_plugin_bibles = $event.target.value"
                       :options="JSON.stringify(bible_options)"
                       @options="bible_options = Object.values($event.target.option_history)"
                       :nonce="nonce"
                       :optionsUrl="language_bibles_options_endpoint"
                       prefetch
                       searchFetch
                       required
                       multiple
            >
            </br-picker>
            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible versions you would like to make available.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group x-show="fields.bible_plugin_bibles && selected_bible_options.length">
            <sp-field-label
                    required
                    for="bible_plugin_bible"
            ><?php esc_html_e( 'Default Bible Version', 'bible-plugin' ); ?></sp-field-label>

            <br-picker id="bible_plugin_bible"
                       name="bible_plugin_bible"
                       placeholder="<?php esc_attr_e( 'Choose', 'bible-plugin' ); ?>..."
                       required
                       :value="fields.bible_plugin_bible"
                       :options="JSON.stringify(selected_bible_options)"
                       @change="fields.bible_plugin_bible = $event.target.value"
            >
            </br-picker>

            <sp-help-text size="s">
				<?php esc_html_e( "Select the bible version that will be used by default.", 'bible-plugin' ); ?>
            </sp-help-text>
        </sp-field-group>

        <sp-field-group x-show="fields.bible_plugin_bibles && Object.values(media_type_options).length">
            <sp-field-label
                    required
                    for="bible_plugin_media_types"
            >
				<?php esc_html_e( 'Media Types', 'bible-plugin' ); ?>
            </sp-field-label>

            <input type="hidden" name="bible_plugin_media_types" x-model="fields.bible_plugin_media_types">

            <sp-field-group horizontal>
                <template x-for="({label, value}) in media_type_options">
                    <sp-checkbox required
                                 size="m"
                                 name="bible_plugin_media_types_checkbox"
                                 @change="$stringable_checkbox_change('fields.bible_plugin_media_types', $event)"
                                 :checked="$as_array('fields.bible_plugin_media_types').includes(value)"
                                 :value="value"
                    >
                        <span x-text="label"></span>
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
