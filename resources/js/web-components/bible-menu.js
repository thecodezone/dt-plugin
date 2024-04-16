import {customElement} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {html, nothing} from "lit";
import {withStores} from "@nanostores/lit";
import {$ntBooks, $otBooks} from "../stores/book.js";
import {$selection} from "../stores/selection.js";
import {__} from "../helpers.js"

@customElement('tbp-bible-menu')
export class BibleMenu extends withStores(TBPElement, [$otBooks, $ntBooks, $selection]) {

    render() {
        return html`
            <sp-action-group style="margin-inline-start: auto;">
                ${$selection.get().length ? html`
                    <tbp-selection-button></tbp-selection-button>
                    <sp-divider
                            size="s"
                            style="align-self: stretch; height: auto;"
                            vertical
                    ></sp-divider>
                ` : nothing}
                <tbp-book-menu label="${__("Old Testament")}" .books=${$otBooks.get()}></tbp-book-menu>
                <tbp-book-menu label="${__("New Testament")}" .books=${$ntBooks.get()}></tbp-book-menu>
            </sp-action-group>
        `;
    }
}