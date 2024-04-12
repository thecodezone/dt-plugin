import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {css, html, nothing} from "@spectrum-web-components/base";
import {$query} from "../stores/query.js"
import {$bookName, $otBooks, $ntBooks, $visitBook} from "../stores/book.js"
import {$fullScreen} from "../stores/full-screen.js";

import {$chapter} from "../stores/chapter.js"
import {__} from "../helpers.js";

@customElement('tbp-bible')
export class Bible extends withStores(TBPElement, [$query, $bookName, $chapter, $otBooks, $ntBooks, $fullScreen]) {
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
                >
                    <div slot="top">
                        ${this.renderHeader()}
                    </div>

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
                <sp-action-group style="margin-inline-start: auto;">
                    <tbp-book-menu label="${__("Old Testament")}" .books=${$otBooks.get()}></tbp-book-menu>
                    <sp-divider
                            size="s"
                            style="align-self: stretch; height: auto;"
                            vertical
                    ></sp-divider>
                    <tbp-book-menu label="${__("New Testament")}" .books=${$ntBooks.get()}></tbp-book-menu>
                </sp-action-group>
            </sp-top-nav>`
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

    renderBooksMenu(label, books) {
        return html`
            <sp-action-menu label="${label}">
                <span slot="label">${label}</span>
                ${books.map((book) => html`
                    <sp-menu-item
                            @click=${() => $visitBook(book)}
                    >${book.name}
                    </sp-menu-item>
                `)}
            </sp-action-menu>`
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