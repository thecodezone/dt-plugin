import {PickerBase} from '@spectrum-web-components/picker';
import chevronStyles from '@spectrum-web-components/icon/src/spectrum-icon-chevron.css.js';
import pickerStyles from '@spectrum-web-components/picker/src/picker.css.js';
import {css, html} from '@spectrum-web-components/base';
import {ifDefined} from '@spectrum-web-components/base/src/directives.js';
import {customElement, property} from '@spectrum-web-components/base/src/decorators.js';
import {createRef, ref} from 'lit/directives/ref.js';


@customElement('br-multi-picker')
export class MultiPicker extends PickerBase {
    /**
     * Represents an array of selected items.
     *
     * @type {Array}
     * @name selectedItems
     * @memberOf global
     */
    @property()
    selectedItems = [];

    /**
     * Represents a search query.
     *
     * @typedef {string} search
     */
    @property({type: String, attribute: 'search', reflect: true})
    search = '';

    /**
     * Indicates whether an options are searchable or not.
     *
     * @type {boolean}
     */
    @property({type: Boolean})
    searchable = false;

    /**
     * The endpoint variable represents the URL of an API endpoint.
     *
     * @type {string}
     */
    @property({type: String})
    endpoint = '';

    /**
     * A unique number or string used to prevent replay attacks or ensure data integrity.
     *
     * @type {string}
     */
    @property({type: String})
    nonce = '';

    @property({type: Array, attribute: 'options'})
    options = []

    @property({type: Number})
    total = 0

    /**
     * Represents a reference object for searching.
     *
     * @typedef {object} SearchRef
     * @property {function} createRef - A function to create a new reference object.
     */
    searchRef = createRef()

    previousSearch = ""

    page = 0

    abortController


    /**
     * Returns an array of CSS styles for the component.
     *
     * @return {Array} Array of CSS styles
     */
    static get styles() {
        return [pickerStyles, chevronStyles, css`
            :host {
                inline-size: fit-content;
                max-inline-size: calc(var(--spectrum-picker-width, var(--spectrum-global-dimension-size-2400)) * 2);
                min-inline-size: var(--spectrum-picker-width, var(--spectrum-global-dimension-size-2400));
            }

            #button {
                block-size: fit-content;
            }

            #selected-labels {
                display: flex;
                flex-wrap: wrap;
                gap: var(--spectrum-spacing-75);
                pointer-events: none;
            }

            :host > #search {
                display: none;
            }

            #search {
                padding: var(--spectrum-global-dimension-size-100) var(--spectrum-global-dimension-size-200) var(--spectrum-global-dimension-size-200) var(--spectrum-global-dimension-size-200);
            }
        `];
    }

    /**
     * Returns the first selected item from the list of selected items.
     * If no item is selected, it returns null.
     *
     * @returns {any|null} The first selected item or null if no item is selected.
     */
    get selectedItem() {
        return this.selectedItems[0] ?? null
    }

    /**
     * Sets the selected item.
     *
     * @param {any} selectedItem - The item to be selected.
     */
    set selectedItem(selectedItem) {
        this.selectedItems.push(selectedItem)
    }


    /**
     * Returns the container styles for the component.
     * If the quiet property is not set, it sets the minimum width to the offset width of the component.
     *
     * @return {Object} The container styles object.
     */
    get containerStyles() {
        const styles = super.containerStyles;
        if (!this.quiet) {
            styles['min-width'] = `${this.offsetWidth}px`;
        }
        return styles;
    }

    /**
     * Renders the menu component
     * @returns {HTMLElement} - The rendered menu component
     */
    get renderMenu() {
        const menu = html`
            ${this.renderSearch()}
            <sp-menu
                    aria-labelledby="applied-label"
                    @change=${this.handleChange}
                    id="menu"
                    @keydown=${{
                        handleEvent: this.handleEnterKeydown,
                        capture: true,
                    }}
                    role=${this.listRole}
                    selects="multiple"
                    valueSelector=","
                    .selected=${this.value ? this.value.split(',') : []}
                    size=${this.size}
                    @sp-menu-item-added-or-updated=${this.shouldManageSelection}
            >
                ${this.getOptions().map((option) => html`
                    <sp-menu-item value="${option.value}" ?disabled="${option.disabled}" :key="${option.value}">
                        ${option.label}
                    </sp-menu-item>
                `)}
                <slot @slotchange=${this.shouldScheduleManageSelection}></slot>
            </sp-menu>
        `;
        this.hasRenderedOverlay =
            this.hasRenderedOverlay ||
            this.focused ||
            this.open ||
            !!this.deprecatedMenu;
        if (this.hasRenderedOverlay) {
            return this.renderOverlay(menu);
        }
        return menu;
    }

    getOptions() {
        const selectedOptions = this.selectedItems.map(el => {
            return {
                value: parseInt(el.value),
                label: el.textContent.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim(),
            }
        })
        const filteredOptions = this.options.filter(option => {
            return !selectedOptions.find(el => el.value === option.value)
        })
        const mergedOptions = [
            ...selectedOptions,
            ...filteredOptions
        ]
        return mergedOptions
    }

    /**
     * The connectedCallback method is called when the element is attached to the DOM.
     * It performs the necessary initialization tasks before rendering the element.
     *
     * @memberof MyElement
     * @return {void}
     */
    connectedCallback() {
        super.connectedCallback()

        setTimeout(() => {
            this.selectedItems = this.menuItems.filter((item) => {
                return this.value.split(',').includes(item.value) && !item.disabled
            });
            this.requestUpdate()
        }, 1);
    }

    /**
     * Renders the search field and help text.
     * @function renderSearch
     * @returns {HTMLElement} The rendered HTML element.
     */
    renderSearch() {
        if (!this.searchable) return html``
        return html`
            <sp-field-group vertical id="search">
                <sp-search quiet :value="${this.search}" ${ref(this.searchRef)} @keydown=${this.handleSearch}>
                </sp-search>
            </sp-field-group>
        `
    }

    /**
     * Handles the search functionality by updating the visibility of menu items based on the search input value.
     *
     * @param {Object} event - The event object containing the search input value.
     */
    handleSearch(event) {
        this.search = event.target.value


        //If enter or return
        if (event.keyCode === 13) {
            event.stopPropagation()
            event.preventDefault()
        }

        this.filter()

        this.dispatchEvent(
            new Event('search', {
                bubbles: true,
                // Allow it to be prevented.
                cancelable: true,
                composed: true,
            })
        );

        this.fetchOptions()
    }

    filter() {
        if (!this.endpoint) {
            this.menuItems.forEach((item) => {
                if (item.textContent.toLowerCase().includes(this.search.toLowerCase())) {
                    item.hidden = false
                } else {
                    item.hidden = true
                }
            })
        }
    }

    fetchOptions() {
        if (this.search !== this.previousSearch) {
            this.page = 1
            this.previousSearch = this.search
        } else {
            this.page++
        }

        if (this.abortController) {
            this.abortController.abort()
        }
        this.abortController = new AbortController()

        const {signal} = this.abortController;

        fetch(`${this.endpoint}?&search=${this.search}&paged=${this.page}`, {
            signal,
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce
            }

        })
            .then(response => response.json())
            .then(data => {
                this.options = data.data
                this.total = data?.meta?.pagination?.total ?? 0
            })
    }

    /**
     * Renders the label content based on the given values and selected items.
     *
     * @returns {TemplateResult} - The rendered label content.
     */
    renderLabelContent() {
        if (this.value && this.selectedItems) {
            return html`
                <div id="selected-labels">
                    ${this.selectedItems.map((item) => html`
                        <sp-badge variant="accent" size="s">
                            ${item.textContent}
                        </sp-badge>`)}
                </div>
            `
        }
        return html`
            <slot name="label" id="label">
                <span
                        aria-hidden=${ifDefined(
                                this.appliedLabel ? undefined : 'true'
                        )}
                >
                    ${this.label}
                </span>
            </slot>
        `;
    }


    /**
     * Handles keydown events.
     *
     * @param {Event} event - The keydown event.
     */
    handleKeydown = (event) => {
        const {code} = event;
        this.focused = true;
        if (!code.startsWith('Arrow') || this.readonly) {
            return;
        }
        if (code === 'ArrowUp' || code === 'ArrowDown') {
            this.toggle(true);
            return;
        }
        event.preventDefault();
        const selectedIndex = this.selectedItem
            ? this.menuItems.indexOf(this.selectedItem)
            : -1;
        // use a positive offset to find the first non-disabled item when no selection is available.
        const nextOffset = selectedIndex < 0 || code === 'ArrowRight' ? 1 : -1;
        let nextIndex = selectedIndex + nextOffset;
        while (
            this.menuItems[nextIndex] &&
            this.menuItems[nextIndex].disabled
            ) {
            nextIndex += nextOffset;
        }
        if (!this.menuItems[nextIndex] || this.menuItems[nextIndex].disabled) {

        }
    };

    /**
     * Handles the change event for a target element.
     *
     * @param {Event} event - The change event triggered by the target element.
     *
     * @return {void} - This method does not return a value.
     */
    handleChange(event) {
        const target = event.target;
        const selectedItems = target.selectedItems;
        if (event.cancelable) {
            this.setValueFromItems(selectedItems, event);
        } else {
            // Non-cancelable "change" events announce a selection with no value
            // change that should close the Picker element.
            this.open = false;
        }
    }

    /**
     * Sets the value of the component based on the provided items.
     *
     * @param {Array} items - An array of items to be used for setting the value.
     * @param {Event} menuChangeEvent - [Optional] The menu change event that triggers the method.
     * @return {Promise} - A promise that resolves when the value has been set.
     */
    async setValueFromItems(
        items,
        menuChangeEvent
    ) {
        const oldSelectedItems = this.selectedItems;
        const oldValue = this.value;
        const values = items.map((item) => item.value);

        // Set a value.
        this.selectedItems = items;
        this.value = values.join(',');
        await this.updateComplete;
        const applyDefault = this.dispatchEvent(
            new Event('change', {
                bubbles: true,
                // Allow it to be prevented.
                cancelable: true,
                composed: true,
            })
        );
        if (!applyDefault && this.selects) {
            if (menuChangeEvent) {
                menuChangeEvent.preventDefault();
            }
            this.selectedItems.forEach((item) => {
                this.setMenuItemSelected(item, false);
            });
            if (oldSelectedItems) {
                this.selectedItems.forEach((item) => {
                    this.setMenuItemSelected(item, false);
                });
                oldSelectedItems.forEach((item) => {
                    this.setMenuItemSelected(item, true);
                });
            }
            this.selectedItems = oldSelectedItems;
            this.value = oldValue;
            this.open = true;

        } else if (!this.selects) {
            // Unset the value if not carrying a selection
            this.selectedItems = oldSelectedItems;
            this.value = oldValue;

        }
    }

    /**
     * Manages the selection of items in a menu.
     *
     * @returns {Promise<void>} A promise that resolves when the selection is managed.
     */
    async manageSelection() {
        if (this.selects == null) return;

        this.selectionPromise = new Promise(
            (res) => (this.selectionResolver = res)
        );
        let selectedItem
        await this.optionsMenu.updateComplete;
        if (this.recentlyConnected) {
            // Work around for attach timing differences in Safari and Firefox.
            // Remove when refactoring to Menu passthrough wrapper.
            await new Promise((res) => requestAnimationFrame(() => res(true)));
            this.recentlyConnected = false;
        }
        let selectedItems = []
        this.menuItems.forEach((item) => {
            if (this.value.split(',').includes(item.value) && !item.disabled) {
                selectedItems.push(item)
            } else {
                item.selected = false;
            }
        });
        this.selectedItems = selectedItems
        if (this.open) {
            await this.optionsMenu.updateComplete;
            this.optionsMenu.updateSelectedItemIndex();
        }
        this.selectionResolver();
        this.willManageSelection = false;
    }
}