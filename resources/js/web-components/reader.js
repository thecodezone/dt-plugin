import {customElement} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {$content} from "../stores/content.js";
import {css, html} from "@spectrum-web-components/base";
import {$referenceData} from "../stores/reference.js";
import {$media_type_key} from "../stores/media-type.js";

@customElement('tbp-reader')
export class Reader extends withStores(TBPElement, [$content, $referenceData]) {
    static get styles() {
        return [
            super.styles,
            css`
                #reader {
                    max-height: 80vh;
                    overflow: auto;
                }
            `
        ];
    }

    render() {
        return html`

            <sp-dialog id="reader" no-divider size="md">
                <tbp-content
                        .content=${$content.get()}
                        .reference="${$referenceData.get()}"
                        type="${$media_type_key.get()}"/>
            </sp-dialog>
        `;
    }
}