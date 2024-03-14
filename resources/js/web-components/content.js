import {customElement, property} from "lit/decorators.js";
import {html} from "lit";
import {SpectrumElement} from "@spectrum-web-components/base";
import {TBPElement} from "./base.js";
import {reference_from_content} from "../helpers.js";

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
     * Retrieves the reference from the content.
     *
     * @returns {Reference} The reference obtained from the content.
     */
    get reference() {
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
            <h2>${this.reference}</h2>
            ${this.renderContent()}
        `;
    }

    /**
     * Renders the content based on the media type.
     *
     * @return {Array|HTMLElement} - The rendered content based on the media type.
     */
    renderContent() {
        switch (this.media_type) {
            case "text":
                return this.content.map(item => this.renderText(item))
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
    renderText(item) {
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
}
