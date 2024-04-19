import {customElement} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {css, html} from "@spectrum-web-components/base";
import {$referenceData} from "../stores/reference.js";
import {$media, findContent} from "../stores/media.js";

@customElement('tbp-reader')
export class Reader extends withStores(TBPElement, [$referenceData, $media]) {
    get content() {
        return findContent('text') ?? [];
    }

    static get styles() {
        return [
            super.styles,
            css`
                #reader {
                    --mod-dialog-confirm-padding-grid: 0;
                    max-height: var(--tbp-reader-height, 75vh);
                    max-width: var(--wp--style--global--wide-size, 1200px);
                    margin: var(--tpb-reader-margin, 25px auto);
                    overflow-y: auto;
                    overflow-x: hidden;
                }
            `
        ];
    }

    render() {
        return html`
            <sp-dialog id="reader" no-divider size="md">
                <tbp-content
                        .reference="${$referenceData.get()}"
                        .content="${this.content}"
                        type="text"
                        selectable
                />
            </sp-dialog>
        `;
    }
}