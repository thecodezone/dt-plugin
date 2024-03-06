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
            this.$watch('fields.languages', this.reset_languages_dependencies.bind(this))
        },

        /**
         * Returns the selected language options.
         * @return {object} The selected language options as an object.
         */
        get selected_language_options() {
            return this.language_options.filter(({value}) => {
                return this.$as_array('fields.languages').includes(value.toString())
            })
        },

        /**
         * Retrieves the language options endpoint for the Bible plugin.
         *
         * @return {string} The language options endpoint.
         */
        get language_bibles_options_endpoint() {
            return this.bible_options_endpoint + "?language_code=" + this.selected_language_options.map(({language_code}) => language_code).join(',')
        },

        /**
         * Resets the dependencies related to languages
         *
         * @since 1.0.0
         *
         * @return {void}
         */
        reset_languages_dependencies() {
            this.fields.language = ""
            this.fields.bibles = ""
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