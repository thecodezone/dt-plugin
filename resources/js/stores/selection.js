import {atom, computed} from "nanostores";
import {reference_from_object} from "../helpers.js";

export const $selection = atom([])

export const $selectionOpen = atom(false)

export const $shareUrl = computed($selection, (selection) => {
    const pageUrl = new URL(window.location.href);
    if (!selection.length) {
        return pageUrl.toString();
    }
    const first = selection[0];
    const last = selection[selection.length - 1];

// new reference
    const newReference = encodeURIComponent(reference_from_object({
        book: first.book,
        chapter: first.chapter,
        verse_start: first.verse_start,
        verse_end: last.verse_end
    }));

// updating the 'reference' query parameter
    pageUrl.searchParams.set('reference', newReference);

// returning the updated URL
    return pageUrl.toString();
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