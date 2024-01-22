export default {
    /**
     * Converts the value of a specified field in an object to an array.
     * If an array is provided, it filters out duplicate values and assigns the filtered values as a comma-separated string to the field.
     * If the field already contains an array, it returns the array.
     * If the field contains a single value (not comma-separated), it returns an array containing the single value.
     * If the field contains multiple values (comma-separated), it splits the values and returns an array.
     *
     * @param {string} field - The field to convert to an array.
     * @param {Array} [array=null] - An optional array to filter and assign to the field.
     * @returns {Array} - The converted array.
     */
    $as_array(field, array = null) {
        if (array) {
            this.$data[field] = array.filter((value, index, self) =>
                value && self.indexOf(value) === index
            ).join(",")
        }
        const current = this.$data[field] ?? ""
        if (Array.isArray(current)) {
            return current;
        }
        if (!current.includes(',')) {
            return [current]
        }
        return current.split(",");
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
        let values = this.$as_array('bible_plugin_media');
        if (el.checked) {
            values.push(el.value);
            this.$as_array('bible_plugin_media', values);
        } else {
            const newValues = values.filter((value) => value !== el.value)
            this.$as_array('bible_plugin_media', newValues);
        }
    }
}