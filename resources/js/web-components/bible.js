import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {css, html} from "@spectrum-web-components/base";
import {$query} from "../stores/query.js"
import {$bookName, $otBooks, $ntBooks, $visitBook} from "../stores/book.js"
import {
    $chapter,
    $hasPreviousChapter,
    $hasNextChapter,
    $visitPreviousChapter,
    $visitNextChapter
} from "../stores/chapter.js"
import {__} from "../helpers.js";

@customElement('tbp-bible')
export class Bible extends withStores(TBPElement, [$query, $bookName, $chapter, $otBooks, $ntBooks]) {
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
            `
        ];
    }

    render() {
        const {loading, data} = $query.get();

        return html`
            <main>
                <sp-top-nav size="m">
                    <sp-top-nav-item size="m"><strong>${loading ? html`
                        <sp-progress-circle
                                size="s"
                                label="Loading bible..."
                                indeterminate
                        ></sp-progress-circle>` : $bookName.get() + " " + $chapter.get()}</strong></sp-top-nav-item>
                    <sp-action-group style="margin-inline-start: auto;">
                        ${this.renderBooksMenu(__("Old Testament"), $otBooks.get())}
                        <sp-divider
                                size="s"
                                style="align-self: stretch; height: auto;"
                                vertical
                        ></sp-divider>
                        ${this.renderBooksMenu(__("New Testament"), $ntBooks.get())}
                    </sp-action-group>
                </sp-top-nav>
                ${loading ? this.renderLoader() : this.renderLoaded()}
            </main>
        `;
    }

    renderLoader() {
        return html`
            <div class="#loader">
            </div>
        `
    }


    renderLoaded() {
        return html`
            <div class="#loaded">
                <tbp-reader></tbp-reader>
                ${this.renderFooter()}
            </div>
        `
    }

    renderBooksMenu(label, books) {
        return html`
            <sp-action-menu>
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
        return html`
            <sp-top-nav quiet>
                <sp-action-group emphasized>
                    ${$hasPreviousChapter.get() ? html`
                        <sp-action-button emphasized @click="${$visitPreviousChapter}">
                            &nbsp;&nbsp;<sp-icon-arrow500 size="m"
                                                          name="ui:Arrow100"
                                                          style="transform: rotate(180deg);">
                        </sp-icon-arrow500>&nbsp;&nbsp;
                        </sp-action-button>` : ''}

                    ${$hasNextChapter.get() ? html`
                        <sp-action-button emphasized @click="${$visitNextChapter}">
                            &nbsp;&nbsp;<sp-icon-arrow500 size="m"
                                                          name="ui:Arrow100">
                        </sp-icon-arrow500>&nbsp;&nbsp;
                        </sp-action-button>` : ''}
                </sp-action-group>
            </sp-top-nav>`
    }
}