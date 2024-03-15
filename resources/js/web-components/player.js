import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import Plyr from 'plyr';
import plyrStyles from 'plyr/dist/plyr.css?inline';
import {css, html, unsafeCSS} from "@spectrum-web-components/base";
import {createRef, ref} from "lit/directives/ref.js";
import svg from 'plyr/dist/plyr.svg?raw'
import {unsafeSVG} from "lit-html/directives/unsafe-svg.js";

@customElement('tbp-player')
export class Scripture extends TBPElement {
    @property({type: Boolean}) uninitialized = false;

    rootRef = createRef()

    static get styles() {
        return [
            unsafeCSS(plyrStyles),
            super.styles,
            css`
                :host {
                    --plyr-color-main: var(--spectrum-accent-color-1300);
                }

                .player__svg {
                    display: none;
                }
            `
        ]
    }

    get el() {
        return this.shadowRoot.querySelector('video,audio')
    }

    connectedCallback() {
        this.addEventListener('tpb-player:initialize', this.init.bind(this))
        super.connectedCallback();
    }

    firstUpdated(_changedProperties) {
        if (!this.uninitialized) {
            this.init()
        }
    }

    render() {
        return html`
            <div class="player__wrapper">
                <div class="player" ${ref(this.rootRef)}>
                    ${this.children}
                </div>
                <div class="player__svg">
                    ${unsafeSVG(svg)}
                </div>
            </div>`
    }

    init() {
        if (!this.el) {
            return
        }
        this.plyr = new Plyr(
            this.el
        )
        this.plyr.play()
    }
}