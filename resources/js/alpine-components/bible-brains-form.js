import form from './form.js'

/**
 * Represents the settings form for modifying the BibleBrain settings.
 */
window.br_bible_brains_form = (props) => {
    return {
        ...form(),
        language_options: [],
        bible_options: [],
        media_options: [],
        language_options_endpoint: "",
        bible_options_endpoint: "",
        ...props,

        /**
         * Initializes the method with necessary watches and bindings
         *
         * @method init
         *
         * @returns {void}
         */
        init() {
            this.watchForAlerts()
            this.$watch('fields.bible_plugin_languages', this.reset_languages_dependencies.bind(this))
            this.$watch('fields.bible_plugin_bibles', this.reset_bibles_dependencies.bind(this))
        },

        /**
         * Returns the selected language options.
         * @return {object} The selected language options as an object.
         */
        get selected_language_options() {
            return this.language_options.filter(({value}) => {
                return this.$as_array('fields.bible_plugin_languages').includes(value.toString())
            })
        },

        /**
         * Retrieves the selected bible options based on the available Bible reader bibles.
         *
         * @returns {Object} Returns an object containing the selected bible options.
         */
        get selected_bible_options() {
            return this.bible_options.filter(({value}) =>
                this.$as_array('fields.bible_plugin_bibles').includes(value.toString()))
        },

        /**
         * Retrieves the language options endpoint for the Bible plugin.
         *
         * @return {string} The language options endpoint.
         */
        get language_bibles_options_endpoint() {
            return this.bible_options_endpoint.replace('{id}', this.fields.bible_plugin_languages)
        },

        /**
         * Resets the dependencies related to languages
         *
         * @since 1.0.0
         *
         * @return {void}
         */
        reset_languages_dependencies() {
            this.fields.bible_plugin_language = ""
            this.fields.bible_plugin_bibles = ""
            this.fields.bible_plugin_bible = ""
            this.fields.bible_plugin_media_types = ""
        },

        /**
         * Resets the bibles and dependencies
         *
         * @return {void}
         */
        reset_bibles_dependencies() {
            this.fields.bible_plugin_bible = ""
            this.fields.bible_plugin_media_types = ""
        },

        /**
         * Changes the value of a stringable checkbox field.
         *
         * @param {string} field - The name of the checkbox field.
         * @param {Event} event - The event triggered by the checkbox change.
         *
         * @return {void}
         */
        $stringable_checkbox_change(field, event) {
            this.$set_stringable_checkbox_value(field, event.target)
        },

        /**
         * Sets the value of a stringable checkbox field.
         *
         * @param {string} field - The field name.
         * @param {HTMLInputElement} el - The checkbox element.
         *
         * @return {void}
         */
        $set_stringable_checkbox_value(field, el) {
            const checkboxes = el.parentElement.querySelectorAll('sp-checkbox')
            const value = Array.from(checkboxes).reduce((array, el) => {
                if (el.checked) {
                    array.push(el.value)
                }
                return array;
            }, [])
            this.$as_array(field, value)
        }
    }
}