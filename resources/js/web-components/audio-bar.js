import {css, html, nothing} from "@spectrum-web-components/base";
import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {$referenceData} from "../stores/reference.js";
import {$media, findContent} from "../stores/media.js";
import {$audio, $hasAudio, $audioOpen} from "../stores/audio.js";

@customElement('tbp-audio-bar')
export class AudioBar extends withStores(TBPElement, [$media, $hasAudio, $audioOpen]) {
    static get styles() {
        return [
            super.styles,
            css`
                :root {
                }

                #inner {
                    display: flex;
                    gap: --spectrum-global-dimension-size-100;
                    align-items: center;
                    width: 100%;
                    padding-left: var(--spectrum-global-dimension-size-100);
                }

                sp-action-bar {
                    width: 100%;
                    display: block;
                    bottom: var(--spectrum-global-dimension-size-200);
                    --mod-actionbar-height: auto;
                    --mod-actionbar-spacing-item-counter-top: 0;
                    --mod-actionbar-spacing-outer-edge: 0;
                }

                sp-icon-audio {
                    transform: translateY(3px);
                }

                tbp-content {
                    display: block;
                    flex-grow: 1;
                }
            `
        ]
    }

    render() {
        if (!$audioOpen.get() || !$hasAudio.get()) {
            return nothing;
        }
        return html`
            <sp-action-bar open="true" @close="${() => $audioOpen.set(false)}" variant="sticky">
                <div id="inner" slot="override">
                    <sp-close-button
                            @click="${() => $audioOpen.set(false)}"></sp-close-button>
                    <tbp-content
                            .reference="${$referenceData.get()}"
                            .content="${$audio.get()}"
                            type="audio"
                            autoplay
                            selectable
                    ></tbp-content>
                </div>


            </sp-action-bar>
        `
    }
}