import {ColorSlider} from '@spectrum-web-components/color-slider';
import {css, html, LitElement} from "lit";
import {customElement} from "lit/decorators.js";
import {Textfield} from "@spectrum-web-components/textfield";
import textFieldStyles from "@spectrum-web-components/textfield/src/textfield.css.js";
import convert from "color-convert";

@customElement('br-color-slider')
export class ColorPicker extends Textfield {
    static get styles() {
        return [
            textFieldStyles,
            css`
                #color-slider {
                    display: flex;
                    gap: 5px;
                }

                :host {
                    --spectrum-textfield-corner-radius: 4px 4px 0 0;
                    --spectrum-color-slider-border-rounding: 0 0 4px 4px;
                }

                sp-color-slider {
                    width: 100%;
                }

                #swatch {
                    width: 30px;
                    height: 30px;
                }
            `,
        ]
    }

    render() {
        return html`
            <div id="color-slider">
                <div>

                    <div id="textfield">
                        ${this.renderField()}
                    </div>

                    ${this.renderSlider()}

                </div>

                <sp-swatch color="${this.value}"></sp-swatch>
            </div>


            ${this.renderHelpText(this.invalid)}
        `;
    }

    renderSlider() {
        return html`
            <sp-color-slider color="${this.value}" @change="${this.handleColorUpdate.bind(this)}"></sp-color-slider>
        `;
    }

    handleColorUpdate(e) {
        this.value = e.target.color;
    }
}