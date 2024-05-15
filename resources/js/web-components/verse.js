import {TBPElement} from "./base.js";
import {customElement, property, query} from "lit/decorators.js";
import {css, html} from "@spectrum-web-components/base";
import {classMap} from "lit/directives/class-map.js";
import interact from 'interactjs';

@customElement('tbp-verse')
export class Scripture extends TBPElement {
    @property({type: String}) verse = '';
    @property({type: String}) text = '';
    @property({type: Boolean}) selectable = false;
    @property({type: Boolean, reflect: true}) selected = false;
    @query('.verse') verseEl;

    tapTimeout = null;
    selectionChangeListener = null;

    createRenderRoot() {
        return this;
    }

    get classMap() {
        const {selected} = this;
        return classMap({
            "verse": true,
            "verse--selected": selected,
        })
    }

    get isSelected() {
        let selection = this.shadowRoot.getSelection();
        return selection.type !== "None";
    }

    connectedCallback() {
        this.selectionChangeListener = this.handleSelectionChange.bind(this);
        super.connectedCallback();
        setTimeout(() => {
            this.proxyEvents();
        });
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        interact(this.verseEl).unset();
    }


    /**
     * Proxy some non-native events from interact.js to lit
     */
    proxyEvents() {
        interact(this.verseEl)
            .on('hold', (event) => {
                const detail = {
                    verse: this.verse,
                    text: this.text,
                }
                this.verseEl.dispatchEvent(new CustomEvent('hold', {bubbles: true, detail}))
                this.dispatchEvent(new CustomEvent('hold', {bubbles: true, detail}))
            });
        interact(this.verseEl)
            .on('tap', (event) => {
                if (event.dt > 300) {
                    clearTimeout(this.tapTimeout);
                    return;
                }

                const detail = {
                    verse: this.verse,
                    text: this.text,
                    double: event.double,
                    dt: event.dt,
                }

                this.verseEl.dispatchEvent(new CustomEvent('tap', {bubbles: true, detail}))
                this.dispatchEvent(new CustomEvent('tap', {bubbles: true, detail}))

                if (event.double) {
                    clearTimeout(this.tapTimeout);
                    this.verseEl.dispatchEvent(new CustomEvent('doubletap', {bubbles: true, detail}))
                    this.dispatchEvent(new CustomEvent('doubletap', {bubbles: true, detail}))
                } else {
                    this.tapTimeout = setTimeout(() => {
                        this.verseEl.dispatchEvent(new CustomEvent('singletap', {bubbles: true, detail}))
                        this.dispatchEvent(new CustomEvent('singletap', {bubbles: true, detail}))
                    }, 300);
                }
            });
    }

    handleSelectionChange() {
        if (!this.isSelected) {
            return;
        }
        let range = document.createRange();
        range.selectNodeContents(this.shadowRoot);
        let selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
    }

    render() {
        return html`
            <span class="${this.classMap}"
                  .aria-selected="${this.selected}"
            >
                <span class="verse__reference">
                    ${this.verse}
                </span>
                ${this.text}
            </span>
        `;
    }
}