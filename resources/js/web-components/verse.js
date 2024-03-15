import {TBPElement} from "./base.js";
import {customElement, property} from "lit/decorators.js";
import {css, html} from "@spectrum-web-components/base";
import {classMap} from "lit/directives/class-map.js";

@customElement('tbp-verse')
export class Scripture extends TBPElement {
    @property({type: String}) verse = '';
    @property({type: String}) text = '';

    static get styles() {
        return [
            super.styles,
            css`
                .verse__reference {
                    color: var(--tpb-verse-reference-color, var(--spectrum-neutral-visual-color, gray));
                    font-weight: var(--tbp-verse-reference-font-weight, normal);
                }

                .verse {
                    line-height: var(--tbp-verse-line-height, 2);
                }
            `];
    }

    get classMap() {
        return classMap({
            "verse": true
        })
    }

    render() {
        return html`
            <span
                    } class="${this.classMap}">
                <span class="verse__reference">${this.verse}</span>
                ${this.text}
            </span>
        `;
    }
}