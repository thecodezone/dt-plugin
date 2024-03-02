import hasArrayable from "./mixins/has-arrayable.js";

/**
 * Alpine component or mixin for handling form validation and submission.
 *
 */
export const form = (props = {}) => {
    return {
        ...hasArrayable,
        nonce: "",
        success: false,
        error: false,
        submitting: false,
        fields: {},
        action: "",
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
        },

        watchForAlerts() {
            this.$watch('success', this.toggleAlerts.bind(this))
            this.$watch('error', this.toggleAlerts.bind(this))
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
            this.success = false
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
                    body: JSON.stringify(this.fields)
                })

                const data = await response.json()

                if (response.status === 200) {
                    this.handleSubmissionSuccess(data.success)
                    return;
                } else if (data.error) {
                    this.handleSubmissionFailed(data.error)
                    return;
                }

                this.handleSubmissionFailed()

            } catch (e) {
                this.handleSubmissionFailed()
            }
        },

        handleSubmissionSuccess(success = true) {
            this.success = success
            this.toggleAlerts()
            this.submitting = false
            this.submissionSucceeded()
        },

        /**
         * Set the necessary state variables when a submission fails.
         *
         * @function handleSubmissionFailed*
         * @returns {void}
         */
        handleSubmissionFailed(error = true) {
            this.success = false
            this.error = error
            this.submitting = false
            this.toggleAlerts()
            this.submissionFailed()
        },

        /**
         * Override this method in the component to handle submission failure.
         *
         * @returns {void}
         */
        submissionFailed() {
        },

        /**
         * Override this method in the component to handle submission success.
         *
         * @returns {void}
         */
        submissionSucceeded() {
        }

    }
}

window.br_form = form

export default form