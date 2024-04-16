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
    mouseDownCoordinates

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
        this.onMouseDown = this.mouseDownListener.bind(this);
        this.onMouseUp = this.mouseUpListener.bind(this);
    }

    connectedCallback() {
        super.connectedCallback();
        $clearSelection()

        setTimeout(() => {
            for (let selectable of this.selectables) {
                selectable.addEventListener('mousedown', this.onMouseDown);
                selectable.addEventListener('mouseup', this.onMouseUp);
            }
        });
    }

    disconnectedCallback() {
        for (let selectable of this.selectables) {
            selectable.removeEventListener('mousedown', this.onMouseDown);
            selectable.removeEventListener('mouseup', this.onMouseUp);
        }
    }

    render() {
        return html`
            <slot></slot>
        `
    }

    mouseDownListener(e) {
        const selectables = Array.from(this.selectables)
        if (selectables.indexOf(e.target) !== -1) {
            this.firstSelected = e.target;
            this.mouseDownCoordinates = {x: e.clientX, y: e.clientY}
        }
    }

    mouseUpListener(e) {
        const selectables = Array.from(this.selectables)
        let selectedIndex = selectables.indexOf(e.target);
        const mouseUpCoordinates = {x: e.clientX, y: e.clientY}
        const distance = Math.sqrt(
            Math.pow(mouseUpCoordinates.x - this.mouseDownCoordinates.x, 2) +
            Math.pow(mouseUpCoordinates.y - this.mouseDownCoordinates.y, 2)
        )
        if (distance < 10) {
            this.firstSelected = null;
            selectables.forEach((el, idx) => {
                el.selected = false;
            })
            this.dispatchSelection()
            return;
        }

        if (this.firstSelected && selectedIndex !== -1) {
            let firstIndex = selectables.indexOf(this.firstSelected);
            let lastIndex = selectedIndex;

            // If selection is backward, swap first and last index
            if (firstIndex > lastIndex) {
                [firstIndex, lastIndex] = [lastIndex, firstIndex];
            }

            let range = document.createRange();
            let selection = this.shadowRoot.getSelection();

            // Select contents from the starting verse's start to the ending verse's end
            range.setStartBefore(selectables[firstIndex]);
            range.setEndAfter(selectables[lastIndex]);

            selection.removeAllRanges();

            selectables.forEach((el, idx) => {
                el.selected = range.intersectsNode(el);
            })

            this.firstSelected = null;

            this.dispatchSelection()
        }
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