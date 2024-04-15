import {customElement, property, state, queryAll} from "lit/decorators.js";
import {html} from "lit";
import {TBPElement} from "./base.js";
import {reference_from_content} from "../helpers.js";
import {css, nothing} from "@spectrum-web-components/base";
import {__} from "../helpers.js";
import {reference_from_object} from "../helpers.js";

/**
 * Represents a custom element called "tbp-content".
 * Extends the TBPElement class.
 */
@customElement('tbp-content')
export class Content extends TBPElement {
    /**
     * Represents an array of content.
     *
     * @type {Array}
     */
    @property({type: Array})
    content = [];

    /**
     * Represents a variable of type string.
     *
     * @typedef {string} TypeString
     */
    @property({type: String})
    type = ""

    /**
     * Determines whether the heading should display or not
     *
     * @param {boolean} heading - The heading value.
     * @returns {boolean} - Display scripture reference if heading is true, hide if false
     */
    @property({type: Boolean})
    heading = false

    /**
     * Custom heading text to display in place of the reference
     */
    @property({type: String})
    heading_text = ""

    @property({type: Object})
    reference = {
        book: "",
        chapter: "",
        verse_start: "",
        verse_end: ""
    }

    @property({type: Boolean})
    selectable = false

    @state()
    openSelection = false

    @state()
    message = false

    @queryAll('[selectable]')
    selectables

    @queryAll('[selected]')
    selectedSelectables

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
        `
    }

    get selections() {
        const groups = []
        this.selectedSelectables.forEach((selectable) => {
            if (!groups.length) {
                groups.push({
                    book: this.reference.book,
                    chapter: this.reference.chapter,
                    verse_start: parseInt(selectable.verse),
                    verse_end: parseInt(selectable.verse),
                })
                return;
            }
            const lastGroup = groups[groups.length - 1]
            if (lastGroup.verse_end + 1 === parseInt(selectable.verse)) {
                lastGroup.verse_end = parseInt(selectable.verse)
                return;
            }
            groups.push({
                book: this.reference.book,
                chapter: this.reference.chapter,
                verse_start: parseInt(selectable.verse),
                verse_end: parseInt(selectable.verse),
            })
        })
        return groups
    }

    findSelection(verse) {
        return this.selections.find((selection) => {
            return selection.verse_start <= parseInt(verse) && selection.verse_end >= parseInt(verse)
        })
    }

    //Without the query
    get pageUrl() {
        return window.location.protocol + "//" + window.location.host + window.location.pathname;
    }

    /**
     * Retrieves the reference from the content.
     *chapter
     * @returns {Reference} The reference obtained from the content.
     */
    get heading_label() {
        if (this.heading_text) {
            return this.heading_text
        }

        if (this.reference
            && this.reference.book
            && this.reference.chapter
            && !this.reference.verse_start) {
            return `${this.reference.book} ${this.reference.chapter}`
        }

        return reference_from_content(this.content)
    }

    /**
     * Retrieves the media type based on the current type.
     * @returns {string|boolean} - The media type or false if the current type is not recognized.
     */
    get media_type() {
        switch (this.type) {
            case "text":
            case "text_plain":
            case "text_json":
            case "text_format":
                return "text"
            case "audio":
                return "audio"
            case "video_stream":
                return "video"
            default:
                return false
        }
    }

    /**
     * Renders the HTML content for the given method.
     *
     * @return {string} The rendered HTML content.
     */
    render() {
        return html`
            ${this.renderMessage()}
            ${this.renderHeading()}
            ${this.renderContent()}
            ${this.renderSelectionModal()}
        `;
    }

    renderHeading() {
        if (this.heading === false) {
            return html``
        }

        return html`
            <h2>${this.heading_label}</h2>
        `
    }


    /**
     * Renders the content based on the media type.
     *
     * @return {Array|HTMLElement} - The rendered content based on the media type.
     */
    renderContent() {
        switch (this.media_type) {
            case "text":
                return this.content.map((item, idx) => this.renderText(item, idx))
            case "audio":
                return this.renderAudio()
            case "video":
                return this.renderVideo()
            default:
                return html``
        }
    }

    /**
     * Renders the text for the given item.
     *
     * @param {object} item - The item to render.
     * @return {string} The rendered text.
     */
    renderText(item, idx) {
        switch (this.media_type) {
            case "text":
                return this.renderVerse(item)
            default:
                return html``
        }
    }

    /**
     * Render a verse
     *
     * @param {object} options - The verse options.
     * @param {number} options.verse_start - The verse start.
     * @param {string} options.verse_text - The verse text.
     * @return {string} The rendered verse HTML.
     */
    renderVerse({verse_start, verse_text}) {
        return html`
            <tbp-verse
                    verse="${verse_start}"
                    text="${verse_text}"
                    selectable="${this.selectable}"
                    @singletap="${(e) => this.handleTap(e)}"
            />
        `
    }

    /**
     * Renders an audio element
     *
     * @return {HTMLTemplateElement} The template element that renders the audio.
     */
    renderAudio() {
        return html`
            <tbp-audio
                    .content="${this.content}"
            />
        `
    }

    /**
     * Renders the video content.
     *
     * @return {string} The rendered HTML for the video content.
     */
    renderVideo() {
        return html`
            <tbp-video
                    .content="${this.content}"
            />
        `
    }

    renderSelectionModal() {
        if (!this.openSelection) {
            return nothing;
        }
        const shareUrl = this.selectionUrl(this.openSelection)
        const shareText = this.selectionText(this.openSelection)

        return html`
            <sp-overlay class="selection_modal" type="modal" ?open="${!!this.openSelection}">
                <sp-dialog-wrapper headline="${__('Selection')}"
                                   size="l"
                                   dismissable
                                   underlay
                                   @close="${this.handleSelectionModalClose.bind(this)}">
                    <div class="selection__copy">
                        <sp-label>${__('Link')}</sp-label>
                        <sp-textfield disabled value="${shareUrl}"></sp-textfield>
                        <sp-button @click="${() => this.copyText(shareUrl)}">
                            ${__('Copy')}
                        </sp-button>
                    </div>

                    <div class="selection__copy">
                        <sp-label>${__('Text')}</sp-label>
                        <sp-textfield disabled multiline value="${shareText}"></sp-textfield>
                        <sp-button @click="${() => this.copyText(shareText)}">
                            ${__('Copy')}
                        </sp-button>
                    </div>
                </sp-dialog-wrapper>
            </sp-overlay>
        `
    }

    renderMessage() {
        if (!this.message) {
            return nothing;
        }
        return html`
            <sp-toast open variant="positive" timeout="6000">
                ${this.message}
            </sp-toast>
        `
    }

    selectionUrl(selection) {
        if (!this.openSelection) {
            return "";
        }
        return `${this.pageUrl}?reference=${encodeURIComponent(reference_from_object(selection))}`
    }

    selectionText(selection) {
        const matchingContent = this.content.filter((item) => {
            return item.verse_start >= selection.verse_start && item.verse_end <= selection.verse_end
        })
        return matchingContent.map((item) => {
            return `${item.verse_start} ${item.verse_text}`
        }).join("\n")
    }

    copyText(text) {
        navigator.clipboard.writeText(text)
        this.clearSelection()
        this.message = __('Copied successfully.')
    }

    handleTap(e) {
        if (this.selectable && e.target.selected) {
            this.handleSelectedTap(e)
        }
    }

    handleSelectedTap(e) {
        if (!this.selectable) {
            return;
        }
        this.message = false
        this.openSelection = this.findSelection(e.target.verse)
    }

    handleSelectionModalClose() {
        setTimeout(() => {
            this.openSelection = false
        }, 1000)
    }

    clearSelection() {
        this.openSelection = false;
        this.selectables.forEach((selectable) => {
            selectable.selected = false;
        })
        this.requestUpdate()
    }
}
