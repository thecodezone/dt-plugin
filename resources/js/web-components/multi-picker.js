import {PickerBase} from '@spectrum-web-components/picker';
import chevronStyles from '@spectrum-web-components/icon/src/spectrum-icon-chevron.css.js';
import pickerStyles from '@spectrum-web-components/picker/src/picker.css.js';
import {css, html} from '@spectrum-web-components/base';
import {ifDefined} from '@spectrum-web-components/base/src/directives.js';

export class MultiPicker extends PickerBase {
    selectedItems = []

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
        `];
    }

    get selectedItem() {
        return this.selectedItems[0] ?? null
    }

    set selectedItem(selectedItem) {
        this.selectedItems.push(selectedItem)
    }


    get containerStyles() {
        const styles = super.containerStyles;
        if (!this.quiet) {
            styles['min-width'] = `${this.offsetWidth}px`;
        }
        return styles;
    }

    get renderMenu() {
        const menu = html`
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

    connectedCallback() {
        super.connectedCallback()

        setTimeout(() => {
            this.selectedItems = this.menuItems.filter((item) => {
                return this.value.split(',').includes(item.value) && !item.disabled
            });
            this.requestUpdate()
        }, 1);

    }

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
            return;
        }
    };

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
            return;
        } else if (!this.selects) {
            // Unset the value if not carrying a selection
            this.selectedItems = oldSelectedItems;
            this.value = oldValue;
            return;
        }
    }

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

window.customElements.define("br-multi-picker", MultiPicker);