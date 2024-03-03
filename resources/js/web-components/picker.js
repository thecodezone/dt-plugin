import {LitElement, css, html} from "lit";
import {ref, createRef} from 'lit/directives/ref.js';
import {customElement, property, state} from 'lit/decorators.js';
import Choices from "choices.js";
import choicesRawCss from "choices.js/public/assets/styles/choices.min.css?inline" with {type: 'css'};
import debounce from "debounce";

const choicesCss = new CSSStyleSheet()
choicesCss.replace(choicesRawCss)

/**
 * Custom element class representing a picker component.
 * @customElement('br-picker')
 */
@customElement('br-picker')
export class Picker extends LitElement {
    abortController

    field = createRef()
    option_history = {}

    @property({type: Boolean}) accessor required = false
    @property({type: Boolean}) accessor multiple = false
    @property({type: String}) accessor placeholder = 'Choose...'
    @property({type: String}) accessor nonce = ''
    @property({type: String, attribute: true}) accessor optionsUrl = ''
    @property({type: Boolean}) accessor prefetch = false
    @property({type: Boolean}) accessor searchFetch = true
    @property({type: String}) accessor name
    @property({type: Array, reflect: true, attribute: true}) accessor options = []

    /**
     * Sets the value property.
     *
     * @param {String | Array | Object} value - The value to be set.
     *
     * @property {String} type - The data type of the value property.
     * @property {Boolean} reflect - Indicates whether changes to the value property should be reflected to the attribute.
     * @property {Boolean} attribute - Indicates whether the value property should be reflected as an attribute.
     *
     * @description This method sets the value property by converting the input value to an array.
     * If the input value is a string, it will be split by comma (',') and stored as an array.
     * If the input value is an array, it will be stored as is.
     * If the input value is an object, its values will be extracted and stored as an array.
     * If the input value is falsy (null, undefined, empty string), an empty array will be stored.
     * If the input value is any other type, it will be converted to a string and split by comma (',') before being stored as an array.
     */
    @property({type: String, reflect: true, attribute: true})
    set value(value) {
        if (value instanceof String) {
            this._value = value.split(',')
            return;
        }
        if (value instanceof Array) {
            this._value = value
            return;
        }
        if (value instanceof Object) {
            this._value = Object(value).values()
            return;
        }
        if (!value) {
            this._value = []
            return;
        }
        this._value = value.toString().split(',')
    }

    get value() {
        return this._value.join(',')
    }

    @state() accessor _value = []
    @state() accessor choices
    @state() accessor dirty = false
    @state() accessor has_updated = false

    /**
     * Set the value of items.
     *
     * @param {*} value - The new value to be set for items.
     */
    set items(value) {
        this.value = value
    }

    /**
     * Retrieves the current value of the items property.
     *
     * @returns {any} The value of the items property.
     */
    get items() {
        return this._value
    }

    /**
     * Retrieves the selected options.
     *
     * @return {Array} selectedOptions - An array containing the selected options.
     */
    get selected_options() {
        return this.items.map(value => this.option_history[value]).filter((option) => option !== undefined)
    }

    /**
     * Returns an array of CSS styles including choices.js CSS
     *
     * @return {Array} The array of CSS styles.
     */
    static get styles() {
        return [choicesCss, css`
            :host {
                inline-size: fit-content;
                overflow: visible;
                max-inline-size: calc(var(--spectrum-picker-width, var(--spectrum-global-dimension-size-2400)) * 2);
                min-inline-size: var(--spectrum-picker-width, var(--spectrum-global-dimension-size-2400));
            }

            * {
                box-sizing: border-box;
            }

            .choices__inner {
                background-color: var(--system-spectrum-picker-background-color-default);
                border-color: var(--system-spectrum-picker-border-color-default);
                border-radius: var(--spectrum-corner-radius-100);
            }

            .choices__list--multiple .choices__item {
                background-color: var(--spectrum-accent-background-color-default);
                border: 1px solid var(--spectrum-accent-background-color-default);
                border-radius: var(--spectrum-component-pill-edge-to-text-100);
            }

            .choices__input {
                background-color: var(--system-spectrum-picker-background-color-default);
            }
        `];
    }

    /**
     * Returns the options for the choices select component.                    this.choices.hideDropdown()
     * @return {object} - An object with options for the choices select component.
     *   - choices {array} - An array of choices options.
     *   - duplicateItemsAllowed {boolean} - Indicates whether duplicate items are allowed.
     *   - removeItemButton {boolean} - Indicates whether the remove item button is enabled.
     *   - placeholderValue {string} - The placeholder value for the choices select component.
     *   - searchEnabled {boolean} - Indicates whether searching is enabled.
     *   - searchChoices {boolean} - Indicates whether to search the choices.
     */
    get choices_options() {
        return {
            allowHTML: true,
            choices: this.options,
            duplicateItemsAllowed: false,
            removeItemButton: this.multiple,
            placeholderValue: this.placeholder,
            searchChoices: !this.optionsUrl
        }
    }

    /**
     * Render method for generating select element
     * @returns {HTMLElement} - Generated select element
     */
    render() {
        return this.multiple ?
            html`<select multiple="${this.multiple}" ${ref(this.field)}/>` :
            html`
                <div @click=${() => this.choices.showDropdown()}><select ${ref(this.field)}/></div>`
    }

    /**
     * Initializes Choices.js when the component has mounted
     *
     * @returns {void}
     */
    firstUpdated() {
        this.init_choices()
    }

    /**
     * Updates the state of the component based on the changed properties.
     *
     * @param {Set} changedProperties - The set of properties that have changed.
     *
     * @return {void}
     */
    updated(changedProperties) {
        //Sync options with Choices
        if (changedProperties.has('options')) {
            this.options.forEach(option => {
                this.option_history[option.value] = option
            })
            setTimeout(() => this.choices.setChoices(this.options, 'value', 'label', true), 10)
            this.dispatchEvent(
                new Event('options', {
                    bubbles: false,
                    cancelable: true,
                    composed: true
                })
            );
        }

        //Sync value with Choices
        if (changedProperties.has('value') && !this.dirty) {
            if (!this.selected_options.length) {
                this.choices.clearStore()
            } else {
                this.choices.setValue(this.selected_options)
            }
        }

        if (changedProperties.has('optionsUrl') && (this.has_updated || this.prefetch)) {
            this.refreshOptions()
        }

        this.has_updated = true
    }


    /**
     * Initializes Choices.js
     */
    init_choices() {
        this.listen()
        this.choices = new Choices(this.field.value, this.choices_options);
        this.choices.setChoices(this.options)
        this.listen(false)
    }

    /**
     * Adds event listeners to the field value element.
     *
     * @method listen
     * @memberof ClassName
     *
     * @return {void}
     */
    listen(before = true) {
        if (before) {
            this.field.value.addEventListener('init', this.handle_init.bind(this))
            this.field.value.addEventListener('change', this.handle_change.bind(this))
            this.field.value.addEventListener('search', debounce(e => this.handle_search(e), 400))
        } else {
            if (this.choices.config.searchEnabled) {
                this.shadowRoot.querySelector("input[type=search]").addEventListener("focus", this.handle_search_focus.bind(this))
            }
        }
    }

    /**
     * Handles the focus event of the search input field.
     * Shows the dropdown choices and clears the interval for hiding the dropdown.
     *
     * @param {Event} e - The focus event object.
     * @return {void}
     */
    handle_search_focus(e) {
        const interval = setInterval(() => this.choices.showDropdown(), 50)
        this.shadowRoot.querySelector("input[type=search]").addEventListener("blur", () => {
            clearInterval(interval)
        })
    }

    /**
     * Dispatches an 'init' event when the component has been initialized.
     *
     * @method handle_init
     * @memberof <component name>
     * @returns {undefined}
     */
    handle_init() {
        this.dispatchEvent(
            new Event('init', {
                bubbles: false,
                cancelable: true,
                composed: true,
            })
        );
    }

    /**
     * Handles the change event.
     *
     * @param {Event} e - The change event.
     * @return {undefined}
     */
    handle_change(e) {
        this.dirty = true
        setTimeout(() => this.dirty = false, 1000)
        this.value = this.choices.getValue(true)
        this.dispatchEvent(
            new Event('change', {
                bubbles: true,
                cancelable: true,
                composed: true,
            })
        );
        this.dispatchEvent(
            new Event('input', {
                bubbles: true,
                cancelable: true,
                composed: true,
            })
        );
    }

    /**
     * Handles the search event.
     *
     * @param {Event} e - The search event object.
     * @return {Promise<void>} - A promise that resolves once the search is complete.
     */
    async handle_search(e) {
        if (!this.searchFetch || !this.optionsUrl || !e.detail.value) {
            return;
        }
        const result = await this.fetch(e.detail.value)
        this.options = result.data
    }

    /**
     * Refreshes the options by fetching data and updating the options property.
     *
     * @async
     * @return {void}
     */
    async refreshOptions() {
        try {
            const result = await this.fetch()
            const {data: options} = await result.json()
            if (options) {
                this.options = options
            }
        } catch (error) {
            console.error(error)
        }
    }

    /**
     * Performs a search by sending a GET request to the options URL with the specified search term.
     *
     * @param {string} term - The search term.
     * @return {Promise} - A Promise that resolves with the JSON response from the server.
     */
    fetch(term) {
        if (!this.optionsUrl) {
            return;
        }

        if (this.abortController) {
            this.abortController.abort()
        }
        this.abortController = new AbortController()

        const {signal} = this.abortController;

        return fetch(`${this.optionsUrl}?&search=${term}`, {
            signal,
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce
            }

        }).then(response => response.json())
    }
}