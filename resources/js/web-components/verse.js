import {TBPElement} from "./base.js";
import {customElement, property} from "lit/decorators.js";
import {css, html} from "@spectrum-web-components/base";
import {classMap} from "lit/directives/class-map.js";

@customElement('tbp-verse')
export class Scripture extends TBPElement {
    @property({type: String}) verse = '';
    @property({type: String}) text = '';

    get classMap() {
        return classMap({
            "verse": true
        })
    }

    render() {
        return html`
            <span class="${this.classMap}">
                <span>${this.verse}</span>
                ${this.text}
            </span>
        `;
    }
}