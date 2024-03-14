import {SpectrumElement} from "@spectrum-web-components/base";
import {css} from "lit";

export class TBPElement extends SpectrumElement {
    static get styles() {
        return [css`
            h1, h2, h3, h4, h5, h6 {
                font-family: var(--tbp-headings-font-family, var(--wp--preset--font-family--heading, var(--spectrum-font-family, serif)));
                font-weight: var(--tbp-headings-font-weight, 600);
            }
        `];
    }
}
