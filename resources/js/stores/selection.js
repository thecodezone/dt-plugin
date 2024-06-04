import {atom, computed} from "nanostores";
import {reference_from_object} from "../helpers.js";

export const $selectables = atom([])

export const $selection = atom([])

export const $hasSelection = computed($selection, (selection) => selection.length > 0)

export const $selectionCount = computed($selection, (selection) =>  selection.length);

export const $selectionOpen = atom(false)

export const $openSelection = () => {
    $selectionOpen.set(true)
}

export const $clearSelection = () => {
    $selection.get().forEach(({selectable}) => {
        selectable.selected = false
    })
    $selection.set([])
    $selectionOpen.set(false)
}
