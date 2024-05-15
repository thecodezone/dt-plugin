import {html} from "lit";
import {customElement, queryAll, state} from "lit/decorators.js";
import {TBPElement} from "./base.js";
import {$clearSelection} from "../stores/selection.js";

@customElement('tbp-selection-manager')
export class SelectionManager extends TBPElement {
    @state()
    firstSelected

    @state()
    lastSelected

    @state()
    mouseDownCoordinates = {x: 0, y: 0}

    @state()
    contextClick = false

    selectionTimeout = null;

    get selectables() {
        return this.querySelectorAll('[selectable]');
    }

    get unselected() {
        return this.querySelectorAll('[selectable][unselected]');
    }

    get selected() {
        return this.querySelectorAll('[selected]');
    }

    constructor() {
        super();

        // Define the listeners in the constructor
        this.onSelectionChange = this.selectionChangeListener.bind(this);
        this.onContextMenu = this.contextMenuListener.bind(this);
    }

    connectedCallback() {
        super.connectedCallback();
        $clearSelection()

        document.addEventListener('selectionchange', this.onSelectionChange)
    }


    selectionChangeListener() {
        const selection = window.getSelection();
        for (let selectable of this.selectables) {
            selectable.selected = selection.containsNode(selectable, true);
        }
        this.dispatchSelection()
    }

    disconnectedCallback() {
        document.removeEventListener('selectionchange', this.onSelectionChange)
        super.disconnectedCallback();
    }

    render() {
        return html`
            <slot></slot>
        `
    }

    contextMenuListener(e) {
        e.preventDefault();
        e.stopPropagation();
        this.contextClick = true;

        if (!e.target.selected) {
            this.selectables.forEach((el, idx) => {
                el.selected = el === e.target;
            })

            this.dispatchSelection();
        }


        setTimeout(() => {
            this.dispatchEvent(new CustomEvent('context', {
                detail: {
                    selectable: e.target,
                    selectables: Array.from(this.selectables),
                    selected: Array.from(this.selected),
                    unselected: Array.from(this.unselected)
                }
            }));
        })


        return false;
    }

    dispatchSelection() {
        setTimeout(() => {
            this.dispatchEvent(new CustomEvent('selection', {
                detail: {
                    selectables: Array.from(this.selectables),
                    selected: Array.from(this.selected),
                    unselected: Array.from(this.unselected)
                }
            }));
        })

    }
}