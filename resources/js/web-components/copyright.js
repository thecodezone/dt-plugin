import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {css, html} from "@spectrum-web-components/base";
import {$copyright} from "../stores/bible.js";

@customElement('tbp-copyright')
export class Bible extends withStores(TBPElement, [$copyright]) {
    static get styles() {
        return [
            super.styles,
            css`
                .copyright {
                    font-size: 0.6rem;
                    text-align: center;
                }
            `
        ];
    }

    render() {
        return html`
            <div class="copyright">
                ${$copyright.get()}
            </div>
        `;
    }
}