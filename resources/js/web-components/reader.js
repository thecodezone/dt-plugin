import {customElement} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {$content} from "../stores/content.js";
import {html} from "@spectrum-web-components/base";
import {$referenceData} from "../stores/reference.js";
import {$media_type_key} from "../stores/media-type.js";

@customElement('tbp-reader')
export class Reader extends withStores(TBPElement, [$content, $referenceData]) {
    render() {
        return html`
            <h2>Reader</h2>
            <div id="reader">
                <tbp-content
                        .content=${$content.get()}
                        .reference="${$referenceData.get()}"
                        type="${$media_type_key.get()}"/>
            </div>
        `;
    }
}