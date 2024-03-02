import {getProperty, setProperty} from 'dot-prop';

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
            setProperty(this.$data, field, array.join(","))
        }
        const current = getProperty(this.$data, field) ?? ''
        if (Array.isArray(current)) {
            return current;
        }
        if (!current.includes(',')) {
            return [current]
        }
        return current.split(",");
    }
}