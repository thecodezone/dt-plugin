import {customElement} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import $reference from "../stores/reference.js";
import {html} from "@spectrum-web-components/base";

@customElement('tbp-reader')
export class Reader extends withStores(TBPElement, [$reference]) {

    render() {
        return html`
            <div id="reader">
                <h1>Reader</h1>
                ${$reference.get()}
            </div>
        `;
    }
}