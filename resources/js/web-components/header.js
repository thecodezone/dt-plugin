import {css, html, nothing} from "@spectrum-web-components/base";
import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {$query} from "../stores/query.js";
import {$bookName} from "../stores/book.js";
import {$chapter} from "../stores/chapter.js";
import {$message} from "../stores/message.js"

@customElement('tbp-header')
export class Header extends withStores(TBPElement, [$query, $bookName, $chapter, $message]) {
    @property({type: String}) text = '';

    static get styles() {
        return [
            super.styles,
            css`
                :host {
                    display: block;
                    margin-bottom: var(--spectrum-global-dimension-size-200);
                    width: 100%;
                }

                tbp-bible-menu {
                    margin-left: auto;
                }

                #message {
                    position: fixed;
                    right: var(--spectrum-global-dimension-size-400);
                    bottom: var(--spectrum-global-dimension-size-400);
                    z-index: var(--tbp-message-z-index, 10000);
                }
            `
        ];
    }

    render() {
        const {loading} = $query.get();

        return html`
            <sp-top-nav size="m">
                <sp-top-nav-item size="m"><strong>${loading ? html`
                    <sp-progress-circle
                            size="s"
                            label="Loading bible..."
                            indeterminate
                    ></sp-progress-circle>` : $bookName.get() + " " + $chapter.get()}</strong></sp-top-nav-item>
                ${this.renderMessage()}
                <tbp-bible-menu></tbp-bible-menu>
            </sp-top-nav>`
    }

    renderMessage() {
        if (!$message.get()) {
            return nothing;
        }
        return html`
            <div id="message">
                <sp-toast open variant="positive"
                          timeout="6000"
                          .key="${$message.get()}" size="s">
                    ${$message.get()}
                </sp-toast>
            </div>
        `
    }
}