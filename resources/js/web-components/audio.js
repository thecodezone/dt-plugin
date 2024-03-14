import {classMap} from "lit/directives/class-map.js";
import {html} from "@spectrum-web-components/base";
import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";

@customElement('tbp-audio')
export class Audio extends TBPElement {
    @property({type: Array}) content = [];

    render() {
        if (!this.content.length) {
            return html``;
        }

        return html`
            <audio controls preload>
                ${this.content.map((item) => {
                    return html`
                        <source src="${item.path}">`;
                })}
            </audio>
        `;
    }
}