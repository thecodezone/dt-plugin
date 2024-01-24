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
        bible_plugin_language: "",
        bible_plugin_version: "",
        bible_plugin_media: "",
        bible_plugin_languages: "",
        bible_plugin_versions: "",
        bible_plugin_bible_brains_key: "",
        dirty_bible_plugin_bible_brains_key: "",
        submitting: false,
        submitting_key: false,
        ...props,

        /**
         * Initializes the method with necessary watches and bindings
         *
         * @method init
         *
         * @returns {void}
         */
        init() {
            this.$watch('bible_plugin_languages', () => this.refresh_version_options.bind(this))
            this.$watch('bible_plugin_languages', this.reset_languages_dependencies.bind(this))
            this.$watch('bible_plugin_versions', this.refresh_media_options.bind(this))
            this.$watch('bible_plugin_versions', this.reset_versions_dependencies.bind(this))
            this.$watch('success', this.toggleAlerts.bind(this))
            this.$watch('error', this.toggleAlerts.bind(this))

            if (this.bible_plugin_bible_brains_key) {
                this.dirty_bible_plugin_bible_brains_key = this.bible_plugin_bible_brains_key
            }
        },

        /**
         * Returns the selected language options.
         * @return {object} The selected language options as an object.
         */
        get selected_language_options() {
            const entries = Object.entries(this.language_options)
            const filtered = entries.filter(([key, value]) => this.$as_array('bible_plugin_languages').includes(key))
            return Object.fromEntries(filtered)
        },

        /**
         * Retrieves the selected version options based on the available Bible reader versions.
         *
         * @returns {Object} Returns an object containing the selected version options.
         */
        get selected_version_options() {
            const entries = Object.entries(this.version_options)
            const filtered = entries.filter(([key, value]) => this.$as_array('bible_plugin_versions').includes(key))
            return Object.fromEntries(filtered)
        },

        get bible_brains_key_verified() {
            return this.dirty_bible_plugin_bible_brains_key === this.bible_plugin_bible_brains_key
        },

        toggleAlerts() {
            this.$refs.success_alert.open = !!this.success
            this.$refs.error_alert.open = !!this.error
        },

        /**
         * Resets the dependencies related to languages in the The Bible Plugin.
         *
         * @since 1.0.0
         *
         * @return {void}
         */
        reset_languages_dependencies() {
            this.bible_plugin_language = ""
            this.bible_plugin_versions = ""
            this.bible_plugin_version = ""
            this.bible_plugin_media = ""
        },

        /**
         * Resets the versions and dependencies for the Bible reader.
         *
         * @return {void}
         */
        reset_versions_dependencies() {
            this.bible_plugin_version = ""
            this.bible_plugin_media = ""
        },

        /**
         * Refreshes the available version options for the Bible reader.
         *
         * @returns {Promise<void>} - A promise that resolves when the version options have been refreshed.
         */
        async refresh_version_options() {
            let response = await fetch(`/bible/api/versions?languages=${this.bible_plugin_languages}`)
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
            let response = await fetch(`/bible/api/media?versions=${this.bible_plugin_versions}`)
            let data = await response.json()

            this.media_options = data.reduce((acc, {key, label}) => {
                acc[key] = label
                return acc
            }, {});
        },

        reset_alerts() {
            this.error = false
            this.message = false
            this.$refs.success_alert.open = false
            this.$refs.error_alert.open = false
        },

        /**
         * Submits the data to the server.
         *
         * @async
         * @return {void}
         */
        async submit(e = null) {
            if (this.submitting) {
                return
            }
            if (e) {
                e.preventDefault()
                e.stopPropagation()
            }

            this.submitting = false
            this.reset_alerts()

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    },
                    body: JSON.stringify({
                        bible_plugin_bible_brains_key: this.bible_plugin_bible_brains_key,
                        bible_plugin_languages: this.bible_plugin_languages,
                        bible_plugin_language: this.bible_plugin_language,
                        bible_plugin_versions: this.bible_plugin_versions,
                        bible_plugin_version: this.bible_plugin_version,
                        bible_plugin_media: this.bible_plugin_media,
                    })
                })

                const data = await response.json()

                if (response.status === 200) {
                    this.message = "Saved"
                    this.success = data.success
                    this.toggleAlerts()
                    this.submitting = false
                    return;
                } else if (data.error) {
                    this.handleSubmissionFailed()
                    this.error = data.error
                    this.toggleAlerts()
                    return;
                }

                this.handleSubmissionFailed()

            } catch (e) {
                this.handleSubmissionFailed()
            }
        },

        handleSubmissionFailed() {
            this.success = false
            this.error = true
            this.submitting = false
            this.submitting_key = false
            this.toggleAlerts()
        },

        async validate_bible_brains_key(e = null) {
            if (this.submitting_key) {
                return
            }

            if (e) {
                e.preventDefault()
                e.stopPropagation()
            }

            this.submitting_key = true
            this.reset_alerts()

            try {
                let dirty_bible_plugin_bible_brains_key = this.dirty_bible_plugin_bible_brains_key
                const response = await fetch(this.key_action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    },
                    body: JSON.stringify({
                        bible_plugin_bible_brains_key: dirty_bible_plugin_bible_brains_key
                    })
                })

                const data = await response.json()

                if (response.status === 200) {
                    this.bible_plugin_bible_brains_key = dirty_bible_plugin_bible_brains_key
                    this.submitting_key = false
                    this.toggleAlerts()
                    this.success = true
                    return;
                } else if (data.error) {
                    this.handleSubmissionFailed()
                    this.error = data.error
                    this.toggleAlerts()
                    return;
                }

                this.handleSubmissionFailed()

            } catch (e) {
                this.handleSubmissionFailed()
            }
        }
    }
}