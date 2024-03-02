/**
 * Represents the settings form for modifying the BibleBrain settings.
 */
window.br_bible_brains_key_form = (props) => {
    return {
        nonce: "",
        success: false,
        error: false,
        bible_plugin_bible_brains_key: "",
        dirty_bible_plugin_bible_brains_key: "",
        success_message: "Success!",
        submitting: false,
        action: "",
        ...props,

        get verified() {
            return this.dirty_bible_plugin_bible_brains_key === this.bible_plugin_bible_brains_key
        },

        /**
         * Initializes the method with necessary watches and bindings
         *
         * @method init
         *
         * @returns {void}
         */
        init() {
            this.$watch('success', this.toggleAlerts.bind(this))
            this.$watch('error', this.toggleAlerts.bind(this))

            if (this.bible_plugin_bible_brains_key) {
                this.dirty_bible_plugin_bible_brains_key = this.bible_plugin_bible_brains_key
            }
        },

        toggleAlerts() {
            this.$refs.success_alert.open = !!this.success
            this.$refs.error_alert.open = !!this.error
        },


        /**
         * Resets the alerts by setting error, message, success_alert.open, and error_alert.open to false.
         *
         * @return {void}
         */
        reset_alerts() {
            this.error = false
            this.message = false
            this.$refs.success_alert.open = false
            this.$refs.error_alert.open = false
        },

        /**
         * Set the necessary state variables when a submission fails.
         *
         * @function handleSubmissionFailed
         * @memberof ClassName
         *
         * @returns {void}
         */
        handleSubmissionFailed() {
            this.success = false
            this.error = true
            this.submitting = false
            this.toggleAlerts()
            if (this.url && document.getElementById('bible-brains-form')) {
                location.reload()
            }
        },

        /**
         * Validates the Bible Brains key.
         *
         * @async
         * @param {Event} e - The event object (optional).
         * @returns {void}
         */
        async validate(e = null) {
            if (this.submitting) {
                return
            }

            if (e) {
                e.preventDefault()
                e.stopPropagation()
            }

            this.submitting = true
            this.reset_alerts()

            try {
                let dirty_bible_plugin_bible_brains_key = this.dirty_bible_plugin_bible_brains_key
                const response = await fetch(this.action, {
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
                    this.submitting = false
                    this.toggleAlerts()
                    this.success = true

                    //If query string tab isn't "bible"
                    if (this.redirect_url && !document.getElementById('bible-brains-form')) {
                        location.reload()
                    }

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