import {atom, map, onSet} from "nanostores"

export const $content = atom([])

export const $searchContent = ({
                                   book_name = null,
                                   book_id = null,
                                   chapter = null,
                                   verse_start = null,
                                   verse_end = null
                               }) => {
    const content = $content.get()
    return content.filter((item) => {
        if (book_name && (item.book_name !== book_name)) return false
        if (book_id && (item.book_id !== book_id)) return false
        if (chapter && item.chapter !== parseInt(chapter)) return false
        if (verse_start && item.verse_start < parseInt(verse_start)) return false
        if (verse_end && item.verse_end > parseInt(verse_end)) return false
        return true
    })
}