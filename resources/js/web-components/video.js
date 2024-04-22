import {css, html, unsafeCSS} from "@spectrum-web-components/base";
import {customElement, property, state} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {createRef, ref} from 'lit/directives/ref.js';
import Hls from "hls.js";

@customElement('tbp-video')
export class Video extends TBPElement {
    @property({type: Array}) content = [];

    @property({type: Boolean, attribute: false}) error = false;

    @property({type: Boolean}) autoplay = false;

    playerRef = createRef();

    videoRef = createRef();

    @state() ready = false;

    get thumbnail() {
        return this.content[0].thumbnail;
    }

    firstUpdated() {
        this.init()
    }

    init() {
        setTimeout(() => {
            const isHls = this.content.some(({path}) => path.endsWith('.m3u8'));
            let initEvent = new CustomEvent('tpb-player:initialize', {
                bubbles: true,
                composed: true
            });

            if (isHls && Hls.isSupported()) {
                const hls = new Hls();
                console.log(this.content)
                this.content.forEach(({path}) => {
                    //hls.loadSource("https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8")
                    console.log(path)
                    hls.loadSource(path);
                });
                hls.attachMedia(this.videoRef.value);
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    this.playerRef.value.dispatchEvent(initEvent);
                });
            } else {
                this.content.forEach(({path}) => {
                    const source = document.createElement('source');
                    source.src = path;
                    this.videoRef.value.appendChild(source);
                });
            }
        }, 10)
    }

    render() {
        return html`
            <tbp-player
                    uninitialized
                    ${ref(this.playerRef)}>
                <video ${ref(this.videoRef)}
                       controls
                       ?autoplay="${this.autoplay}"
                       poster="${this.thumbnail}"></video>
            </tbp-player>`;

    }
}