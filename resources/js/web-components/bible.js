import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {css, html, nothing} from "@spectrum-web-components/base";
import {$query} from "../stores/query.js"
import {$bookName} from "../stores/book.js"
import {$fullScreen} from "../stores/full-screen.js";
import {$chapter} from "../stores/chapter.js"
import {$message} from "../stores/message.js"

@customElement('tbp-bible')
export class Bible extends withStores(TBPElement, [$query, $bookName, $chapter, $fullScreen, $message]) {
    @property({type: String}) version = 'tbp';

    static get styles() {
        return [
            super.styles,
            css`
                main {
                    min-height: 80vh;
                }

                #loader {
                    min-height: 80vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100%;
                }

                .copyright {
                    font-size: 0.6rem;
                    text-align: center;
                }

                tbp-bible-menu {
                    margin-left: auto;
                }

                footer {
                    display: flex;
                    justify-content: space-between;
                    gap: 1rem;
                }
            `
        ];
    }

    firstUpdated(_changedProperties) {
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                $fullScreen.set(false);
            }
        });
    }

    render() {
        return html`
            <main>
                ${$fullScreen.get() ? this.renderFullScreen() : this.renderInPage()}
            </main>
        `;
    }

    renderFullScreen() {
        const {loading} = $query.get();

        return html`
            <sp-overlay type="page" open headline="Full Screen">
                <tbp-dialog-wrapper
                        mode="fullscreenTakeover"
                        no-divider
                        headline-visibility="hidden"
                >
                    ${this.renderHeader()}

                    ${loading ? this.renderLoader() : html`
                        <tbp-reader></tbp-reader>`}

                    ${this.renderFooter()}

                </tbp-dialog-wrapper>
            </sp-overlay>
        `
    }

    renderHeader() {
        const {loading} = $query.get();

        return html`
            <sp-top-nav size="m">
                <sp-top-nav-item size="m"><strong>${loading ? html`
                    <sp-progress-circle
                            size="s"
                            label="Loading bible..."
                            indeterminate
                    ></sp-progress-circle>` : $bookName.get() + " " + $chapter.get()}</strong></sp-top-nav-item>
                ${this.renderMessage()}
                <tbp-bible-menu></tbp-bible-menu>
            </sp-top-nav>`
    }

    renderMessage() {
        if (!$message.get()) {
            return nothing;
        }
        return html`
            <div id="message">
                <sp-toast open variant="positive"
                          timeout="6000"
                          .key="${$message.get()}" size="s">
                    ${$message.get()}
                </sp-toast>
            </div>
        `
    }

    renderInPage() {
        const {loading} = $query.get();

        return html`
            ${this.renderHeader()}
            ${loading ? this.renderLoader() : html`
                <tbp-reader></tbp-reader>`}
            ${this.renderFooter()}
        `
    }

    renderLoader() {
        return html`
            <div class="#loader">
            </div>
        `
    }

    renderFooter() {
        const {loading} = $query.get();

        if (loading) {
            return nothing;
        }

        return html`
            <footer>
                <tbp-chapter-nav></tbp-chapter-nav>
                <tbp-copyright></tbp-copyright>
                <sp-button-group>
                    <sp-button icon-only variant="secondary" @click=${() => $fullScreen.set(!$fullScreen.get())}>
                        <iconify-icon icon="${"ic:round-fullscreen"}"
                                      slot="icon"
                                      width="18px"
                                      height="18px"></iconify-icon>
                    </sp-button>
                </sp-button-group>
            </footer>`
    }
}