import {css, html, LitElement} from "lit";
import uniqua from "uniqua";
import {customElement, property, state} from "lit/decorators.js";

@customElement('br-overlay-picker')

export class OverlayPicker extends LitElement {
    @property({type: Array, reflect: true, attribute: true}) accessor options = [];
    @property({type: String, reflect: true, attribute: true}) accessor optionsUrl = [];
    @property({type: String, reflect: true, attribute: true}) accessor label = ''
    @property({type: String}) accessor nonce = '';
    @property({type: Boolean}) accessor prefetch = false
    @property({type: Boolean}) accessor searchFetch = true
    @property({type: String}) accessor value = ''
    @property({type: String}) accessor searchLabel = 'Search'
    @property({type: Boolean, attribute: true}) accessor searchable = false

    @state() accessor option_history = {}
    @state() accessor selectedOptions = {}
    @state() accessor values = []
    @state() accessor search = ''


    /**
     * Returns an array of CSS styles including choices.js CSS
     *
     * @return {Array} The array of CSS styles.
     */
    static get styles() {
        return [css`
            sp-tag[deletable],
            sp-tag[deletable] * {
                cursor: pointer;
                box-sizing: border-box;
            }

            .multiselect {
                max-width: 400px;
                display: flex;
                flex-direction: column;
                gap: 10px;;
            }

            .multiselect__menu {
                max-height: 90vh;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            sp-search {
                width: 100%;
            }

            sp-menu {
                overflow: hidden;
            }
        `];
    }

    init() {
        this.handleOptionChange()
        this.handleValueChange()
    }

    firstUpdated() {
        if (this.prefetch) {
            this.refreshOptions()
        }
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
            this.handleOptionChange()
        }

        if (changedProperties.has('value')) {
            this.handleValueChange()
        }

        if (changedProperties.has('optionsUrl') && (this.has_updated || this.prefetch)) {
            this.refreshOptions()
        }

        this.has_updated = true
    }

    handleOptionChange() {
        this.options.forEach(option => {
            this.option_history[option.value] = option
        })
        this.dispatchEvent(
            new Event('options', {
                bubbles: false,
                cancelable: true,
                composed: true
            })
        );
    }

    handleValueChange() {
        this.values = this.value.split(',')
        this.selectedOptions = this.values.map(value => this.option_history[value]).filter(option => !!option)
        this.dispatchEvent(
            new Event('input', {
                bubbles: false,
                cancelable: true,
                composed: true
            })
        );
        this.dispatchEvent(
            new Event('change', {
                bubbles: false,
                cancelable: true,
                composed: true
            })
        );
    }

    render() {
        return html`
            <div class="multiselect">

                <sp-action-group size="m" emphasized>
                    ${Object.values(this.selectedOptions).map(({value, itemText}) =>
                            html`
                                <sp-tag deletable @click="${() => this.removeValue(value)}">${itemText}</sp-tag>
                            `
                    )}
                    <sp-action-button emphasized selected @click="${this.searching = true}" id="search">
                        <sp-icon-magnify slot="icon"></sp-icon-magnify>
                    </sp-action-button>
                </sp-action-group>

                <sp-overlay trigger="search@click" type="modal">
                    <sp-dialog-wrapper
                            dismissable
                            underlay
                            headline="${this.label}"
                    >
                        <div class="multiselect__menu">
                            ${this.searchable ? html`
                                <sp-search quiet value="${this.search}"
                                           @input="${this.handleSearch}"></sp-search>` : ''}

                            <sp-menu
                                    selects="multiple"
                            >
                                ${this.options.map(({value, itemText}) =>
                                        html`
                                            <sp-menu-item value="${value}"
                                                          @click="${this.handleOptionClick}"
                                                          ?selected=${this.isValueSelected(value)}>
                                                ${itemText}
                                            </sp-menu-item>
                                        `
                                )}
                            </sp-menu>

                        </div>

                    </sp-dialog-wrapper>
                </sp-overlay>
            </div>
        `;
    }

    handleOptionClick(e) {
        if (this.isValueSelected(e.target.value)) {
            this.removeValue(e.target.value)
            e.target.selected = false
        } else {
            this.addValue(e.target.value)
            e.target.selected = true
        }
    }

    addValue(value) {
        this.value = uniqua(this.values.concat(value)).join(',')
    }

    isValueSelected(value) {
        return this.value.includes(value)
    }

    async handleSearch(e) {
        if (!this.searchFetch || !this.optionsUrl || !e.target.value) {
            return;
        }
        try {
            const result = await this.fetchOptions(e.target.value)
            const {data: options} = await result.json()
            if (options) {
                this.options = options
            }
        } catch (error) {
            console.error(error)
        }
    }

    removeValue(value) {
        this.value = this.values.filter(v => v !== value).join(',')
    }

    /**
     * Refreshes the options by fetching data and updating the options property.
     *
     * @async
     * @return {void}
     */
    async refreshOptions() {
        try {
            const result = await this.fetchOptions()
            const {data: options} = await result.json()
            if (options) {
                this.options = options
            }
        } catch (error) {
            console.error(error)
        }

    }

    /**
     * Performs a search by sending a GET request to the options URL with the specified search searchTerm.
     *
     * @param {string} searchTerm - The search searchTerm.
     * @return {Promise} - A Promise that resolves with the JSON response from the server.
     */
    fetchOptions(searchTerm = '') {
        if (!this.optionsUrl) {
            return;
        }

        if (this.abortController) {
            this.abortController.abort()
        }
        this.abortController = new AbortController()

        const {signal} = this.abortController;

        return fetch(`${this.optionsUrl}?&search=${searchTerm}`, {
            signal,
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce
            }
        })
    }
}