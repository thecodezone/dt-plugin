import hasArrayable from "./mixins/has-arrayable.js";
import hasChildFields from "./mixins/has-child-fields.js";

/**
 * Represents the settings form for modifying the BibleBrain settings.
 */
window.br_bible_brains_form = (props) => {
    return {
        ...hasArrayable,
        ...hasChildFields,
        nonce: "",
        success: false,
        error: false,
        language_options: {},
        version_options: {},
        media_options: {},
        bible_reader_language: "",
        bible_reader_version: "",
        bible_reader_media: "",
        bible_reader_languages: "",
        bible_reader_versions: "",
        submission_key: "",
        ...props,

        /**
         * Initializes the method with necessary watches and bindings
         *
         * @method init
         *
         * @returns {void}
         */
        init() {
            this.$watch('bible_reader_languages', () => this.refresh_version_options.bind(this))
            this.$watch('bible_reader_languages', this.reset_languages_dependencies.bind(this))
            this.$watch('bible_reader_versions', this.refresh_media_options.bind(this))
            this.$watch('bible_reader_versions', this.reset_versions_dependencies.bind(this))
        },

        /**
         * Returns the selected language options.
         * @return {object} The selected language options as an object.
         */
        get selected_language_options() {
            const entries = Object.entries(this.language_options)
            const filtered = entries.filter(([key, value]) => this.$as_array('bible_reader_languages').includes(key))
            return Object.fromEntries(filtered)
        },

        /**
         * Retrieves the selected version options based on the available Bible reader versions.
         *
         * @returns {Object} Returns an object containing the selected version options.
         */
        get selected_version_options() {
            const entries = Object.entries(this.version_options)
            const filtered = entries.filter(([key, value]) => this.$as_array('bible_reader_versions').includes(key))
            return Object.fromEntries(filtered)
        },

        /**
         * Resets the dependencies related to languages in the Bible Reader.
         *
         * @since 1.0.0
         *
         * @return {void}
         */
        reset_languages_dependencies() {
            this.bible_reader_language = ""
            this.bible_reader_versions = ""
            this.bible_reader_version = ""
            this.bible_reader_media = ""
        },

        /**
         * Resets the versions and dependencies for the Bible reader.
         *
         * @return {void}
         */
        reset_versions_dependencies() {
            this.bible_reader_version = ""
            this.bible_reader_media = ""
        },

        /**
         * Refreshes the available version options for the Bible reader.
         *
         * @returns {Promise<void>} - A promise that resolves when the version options have been refreshed.
         */
        async refresh_version_options() {
            let response = await fetch(`/bible/api/versions?languages=${this.bible_reader_languages}`)
            let data = await response.json()

            this.version_options = data.reduce((acc, {bible_id, bible_name}) => {
                acc[bible_id] = bible_name
                return acc
            }, {});
            s
        },

        /**
         * Refreshes the media options based on the selected Bible reader versions.
         * Makes an asynchronous fetch call to the `/bible/api/media` endpoint with the selected versions.
         * Retrieves the response data and transforms it into an object where the keys are the keys from the response data
         * and the values are the labels from the response data.
         * The resulting object is assigned to `this.media_options`.
         *
         * @returns {Promise<void>} - A promise that resolves when the media options have been refreshed.
         */
        async refresh_media_options() {
            let response = await fetch(`/bible/api/media?versions=${this.bible_reader_versions}`)
            let data = await response.json()

            this.media_options = data.reduce((acc, {key, label}) => {
                acc[key] = label
                return acc
            }, {});
        },

        /**
         * Submits the data to the server.
         *
         * @async
         * @return {void}
         */
        async submit(e = null) {
            if (e) {
                e.preventDefault()
                e.stopPropagation()
            }

            this.submission_key = Date.now()
            this.error = false
            this.message = false
            this.$refs.successAlert.open = true
            this.$refs.errorAlert.open = true

            try {
                const response = await fetch('/bible/api/bible-brains', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    },
                    body: JSON.stringify({
                        bible_reader_languages: this.bible_reader_languages,
                        bible_reader_language: this.bible_reader_language,
                        bible_reader_versions: this.bible_reader_versions,
                        bible_reader_version: this.bible_reader_version,
                        bible_reader_media: this.bible_reader_media,
                    })
                })

                const data = await response.json()

                if (response.status === 200) {
                    this.message = "Saved"
                    this.success = data.success
                    return;
                }

                if (response.status === 422) {
                    this.error = data.error
                    return;
                }

                this.error = true
                console.error(data)

            } catch (e) {
                this.error = true
                console.error(e.message)
            }
        }
    }
}