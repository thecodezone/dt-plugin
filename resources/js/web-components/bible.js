import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {withStores} from "@nanostores/lit";
import {html} from "@spectrum-web-components/base";
import {$query} from "../stores/query.js"

@customElement('tbp-bible')
export class Bible extends withStores(TBPElement, [$query]) {
    @property({type: String}) version = 'tbp';


    render() {
        const {loading, data} = $query.get();

        return html`
            <main>
                <sp-action-bar open>
                    <sp-action-group slot="override">
                        <sp-action-button>Medium 1</sp-action-button>
                        <sp-action-button>Medium 2</sp-action-button>
                    </sp-action-group>
                </sp-action-bar>
                ${loading ? html`
                    <sp-progress-circle
                            label="Loading bible..."
                            indeterminate
                            size="l"
                    ></sp-progress-circle>` : html`
                    <tbp-reader/>`}
            </main>
        `;
    }
}