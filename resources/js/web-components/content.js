import {customElement, property, state, queryAll} from "lit/decorators.js";
import {html} from "lit";
import {TBPElement} from "./base.js";
import {reference_from_content} from "../helpers.js";
import {css, nothing} from "@spectrum-web-components/base";
import {__} from "../helpers.js";
import {reference_from_object} from "../helpers.js";
import {$selection, $clearSelection} from "../stores/selection.js";
import {withStores} from "@nanostores/lit";

/**
 * Represents a custom element called "tbp-content".
 * Extends the TBPElement class.
 */
@customElement('tbp-content')
export class Content extends withStores(TBPElement, [$selection]) {
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
     * @returns {string} The rendered HTML content.
     */
    render() {
        if (this.selectable) {
            return html`
                <tbp-selection-manager
                        @selection="${this.handleSelection.bind(this)}"
                >
                    ${this.renderSections()}
                </tbp-selection-manager>
            `
        }

        return this.renderSections();
    }

    renderSections() {
        return html`
            ${this.renderHeading()}
            ${this.renderContent()}
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


    handleSelection(e) {
        $selection.set(e.detail.selected.map((selectable) => {
            return {
                selectable: selectable,
                text: selectable.shadowRoot.textContent.replace(/[\r\n]+/gm, ' ').trim().replace(/\s\s+/g, ' '),
                book: this.reference.book,
                chapter: this.reference.chapter,
                verse_start: parseInt(selectable.verse),
                verse_end: parseInt(selectable.verse),
            }
        }))
    }
}
