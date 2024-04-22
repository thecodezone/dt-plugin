import {classMap} from "lit/directives/class-map.js";
import {html} from "@spectrum-web-components/base";
import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";

@customElement('tbp-audio')
export class Audio extends TBPElement {
    @property({type: Array}) content = [];
    @property({type: Boolean}) autoplay = false;

    render() {
        if (!this.content.length) {
            return html``;
        }

        return html`
            <tbp-player>
                <audio
                        controls
                        preload
                        ?autoplay="${this.autoplay}"
                >
                    ${this.content.map((item) => {
                        return html`
                            <source src="${item.path}">`;
                    })}
                </audio>
            </tbp-player>

        `;
    }
}