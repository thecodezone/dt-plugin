import {customElement, property, query, state} from "lit/decorators.js";
import {withStores} from "@nanostores/lit";
import {TBPElement} from "./base.js";
import {$selection, $shareUrl, $shareText, $clearSelection} from "../stores/selection.js";
import {html} from "lit";
import {__} from "../helpers.js";
import {css} from "@spectrum-web-components/base";
import {$book} from "../stores/book.js";
import {$displayMessage} from "../stores/message.js";

@customElement('tbp-selection-button')
export class SelectionButton extends withStores(TBPElement, [$selection, $shareUrl, $shareText, $book]) {
    @property({type: Boolean})
    open = false

    @state()
    openSelection = false

    @state()
    message = false

    @query('sp-dialog-wrapper')
    dialog

    static get styles() {
        return css`
            sp-toast {
                position: fixed;
                right: 50%;
                transform: translate(50%, 0);
                top: 50%;
            }

            .selection__copy {
                display: flex;
                gap: 1rem;
                align-items: flex-start;
                margin-bottom: .5rem;

                sp-textfield {
                    flex: 1;
                    width: 100%;
                    display: block;
                }
            }

            .count {
                display: block;
                border-radius: 50%;
            }
        `
    }

    render() {
        return html`
            <sp-action-button
                    id="trigger"
                    emphasized
                    selected
            >
                <b slot="icon" class="count">
                    ${$selection.get().length}
                </b>
                ${__("Selected")}
            </sp-action-button>
            <sp-overlay trigger="trigger@click" class="selection_modal" type="modal">
                <sp-dialog-wrapper headline="${__('Selection')}"
                                   size="l"
                                   dismissable
                                   underlay
                                   @close="${this.handleClose.bind(this)}">
                    <div class="selection__copy">
                        <sp-label>${__('Link')}</sp-label>
                        <sp-textfield disabled value="${$shareUrl.get()}"></sp-textfield>
                        <sp-button @click="${() => this.copyText($shareUrl.get())}">
                            ${__('Copy')}
                        </sp-button>
                    </div>

                    <div class="selection__copy">
                        <sp-label>${__('Text')}</sp-label>
                        <sp-textfield disabled multiline value="${$shareText.get()}"></sp-textfield>
                        <sp-button @click="${() => this.copyText($shareText.get())}">
                            ${__('Copy')}
                        </sp-button>
                    </div>
                </sp-dialog-wrapper>
            </sp-overlay>
        `
    }

    handleClose() {
        $clearSelection()
    }

    copyText(text) {
        navigator.clipboard.writeText(text)
        $displayMessage(__('Copied to clipboard'))
        this.close()
    }

    close() {
        this.dialog.close()
    }
}