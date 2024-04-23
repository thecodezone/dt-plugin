import {atom, computed} from "nanostores";
import {reference_from_object} from "../helpers.js";

export const $selection = atom([])

export const $selectionOpen = atom(false)

export const $shareUrl = computed($selection, (selection) => {
    const pageUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}`
    if (!selection.length) {
        return pageUrl
    }
    const first = selection[0]
    const last = selection[selection.length - 1]
    return `${pageUrl}?reference=${encodeURIComponent(reference_from_object({
        book: first.book,
        chapter: first.chapter,
        verse_start: first.verse_start,
        verse_end: last.verse_end
    }))}`
});

export const $shareText = computed($selection, (selection) => {
    return selection.map((item) => {
        return item.text
    }).join("\n")
})

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