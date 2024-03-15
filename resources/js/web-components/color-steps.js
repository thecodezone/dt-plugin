import {customElement, property} from "lit/decorators.js";
import {css, html, LitElement} from "lit";
import {range, lightestShade, darkestShade} from "../helpers.js"
import {TinyColor} from '@ctrl/tinycolor';

@customElement('tbp-color-steps')
export class ColorSteps extends LitElement {

    @property({type: String}) color;
    @property({type: Array}) range = range(100, 900, 100);
    @property({type: Object}) value = {};

    static get styles() {
        return css`
            :host {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: fit-content;
            }`
    }

    updated(changedProperties) {
        if (changedProperties.has('value')) {
            this.dispatchEvent(new CustomEvent('change', {detail: this.value}));
        }

        if (changedProperties.has('range') || changedProperties.has('color')) {
            this.calculateSteps()
        }
    }

    render() {
        return html`
            <sp-swatch-group>
                ${this.range.map(this.renderSwatch.bind(this))}
            </sp-swatch-group>
        `
    }

    renderSwatch(i) {
        const color = this.value[i] ?? null;
        return html`
            <sp-swatch color="${color}"></sp-swatch>`
    }


    calculateSteps() {
        const hslColor = new TinyColor(this.color).toHsl();

        // darkest color has lightness 0
        const darkest = new TinyColor({...hslColor, l: .2}).toHsl();

        // lightest color has lightness 100
        const lightest = new TinyColor({...hslColor, l: .6}).toHsl();

        const midStep = Math.ceil(this.range.length / 2);
        let colorSteps = {};

        for (let i = 0; i < midStep; i++) {
            let l = darkest.l + (i / (midStep - 1)) * (hslColor.l - darkest.l);
            colorSteps[this.range[i]] = new TinyColor({...hslColor, l: l}).toRgbString();
        }

        for (let i = midStep; i < this.range.length; i++) {
            let l = hslColor.l + ((i - midStep + 1) / (this.range.length - midStep)) * (lightest.l - hslColor.l);
            colorSteps[this.range[i]] = new TinyColor({...hslColor, l: l}).toRgbString();
        }

        console.log(colorSteps)

        this.value = colorSteps;
    }
}