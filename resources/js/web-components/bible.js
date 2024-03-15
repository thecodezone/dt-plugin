import {customElement, property} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import $router from "../router.js";
import $route from "../stores/route.js";
import {withStores} from "@nanostores/lit";
import {LitElement} from "lit";
import {html} from "@spectrum-web-components/base";

@customElement('tbp-bible')
export class Bible extends withStores(TBPElement, [$route]) {

    @property({type: String})

    router = null;

    get outlet() {
        return this.shadowRoot.querySelector('#outlet')
    }

    firstUpdated() {
        this.router = $router()
        this.renderRoute($route.get())
        $route.subscribe((route) => {
            this.renderRoute(route)
        })
    }

    renderRoute(route) {
        let component = document.createElement(route);
        this.outlet.innerHTML = '';
        this.outlet.appendChild(component);
    }

    render() {
        return html`
            <main>
                <div id="outlet"></div>
            </main>
        `;
    }
}