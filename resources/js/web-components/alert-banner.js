import {ActionBar} from "@spectrum-web-components/action-bar";
import actionBarStyles from "@spectrum-web-components/action-bar/src/action-bar.css.js";
import {css, html} from '@spectrum-web-components/base';
import {ifDefined} from '@spectrum-web-components/base/src/directives.js';

export class AlertBanner extends ActionBar {
    static get styles() {
        return [
            actionBarStyles,
            css`
                :host {
                    --default-alertbanner-accent-color: white;
                    --default-alertbanner-positive-color: white;
                    --default-alertbanner-negativ-color: white;
                    --mod-closebutton-icon-color-default: white;
                    --default-alertbanner-spacing-start: var(--spectrum-spacing-200);
                    padding-right: 0;
                    padding-left: 0;
                    width: fit-content;
                }

                .field-label {
                    margin-left: var(
                            --default-alertbanner-spacing-start,
                            var(--spectrum-alias-size-100)
                    );
                }

                :host([accent]) #popover {
                    color: var(
                            --default-alertbanner-accent-color,
                            var(--mod-alertbanner-accent-color)
                    );
                    background-color: var(
                            --spectrum-accent-background-color-default,
                            var(--mod-alertbanner-accent-background-color)
                    );
                }

                :host([positive]) #popover {
                    color: var(
                            --default-alertbanner-positive-color,
                            var(--mod-alertbanner-positive-color)
                    );
                    background-color: var(
                            --spectrum-positive-background-color-default,
                            var(--mod-alertbanner-positive-background-color)
                    );
                }

                :host([negative]) #popover {
                    color: var(
                            --default-alertbanner-positive-color,
                            var(--mod-alertbanner-negative-color)
                    );
                    background-color: var(
                            --spectrum-negative-background-color-default,
                            var(--mod-alertbanner-negative-background-color)
                    );
                }

                :host([positive]) .field-label {
                    color: var(
                            --mod-actionbar-emphasized-item-counter-color,
                            var(--spectrum-actionbar-positive-item-counter-color)
                    );
                }

                :host([negative]) .field-label {
                    color: var(
                            --mod-actionbar-emphasized-item-counter-color,
                            var(--spectrum-actionbar-negative-item-counter-color)
                    );
                }

                :host([accent]) .field-label {
                    color: var(
                            --mod-actionbar-emphasized-item-counter-color,
                            var(--spectrum-actionbar-accent-item-counter-color)
                    );
                }
            `];

    }

    render() {
        return html`
            <sp-popover ?open=${this.open} id="popover">
                <slot name="override">
                    <sp-field-label class="field-label">
                        <slot></slot>
                    </sp-field-label>
                    <sp-action-group
                            class="action-group"
                            quiet
                            static=${ifDefined(
                                    this.emphasized ? 'white' : undefined
                            )}
                    >
                        <slot name="buttons"></slot>
                    </sp-action-group>
                    <sp-close-button
                            static=${ifDefined(
                                    this.emphasized ? 'white' : undefined
                            )}
                            class="close-button"
                            label="Clear selection"
                            @click=${this.handleClick}
                    ></sp-close-button>
                </slot>
            </sp-popover>
        `;
    }
}

window.customElements.define("tbp-alert-banner", AlertBanner);