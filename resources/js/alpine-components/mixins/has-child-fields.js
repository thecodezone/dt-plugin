export default {
    /**
     * Refreshes the value of the childField based on the parentField.
     * If the parentField value is an object, it checks if the childField value exists in the keys of the parentField object.
     * If the childField value is not found in the parentField keys, sets the childField value to an empty string.
     *
     * @param {string} childField - The field to refresh the value of.
     * @param {string} parentField - The field used to determine the value of the childField.
     *
     * @return {void}
     */
    $refresh_child_value(childField, parentField) {
        let parentValue = this.$data[parentField]
        let childValue = this.$data[childField]

        if (typeof parentValue === 'object') {
            parentValue = Object.keys(parentValue)
        }
        if (!parentValue.includes(childValue)) {
            this.$data[childField] = ""
        }
    },
}