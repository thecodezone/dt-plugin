import {queryParam} from "../nanostore/query-param.js";
import {computed} from "nanostores"
import {$chapter} from "./chapter.js";
import {$verse_start, $verse_end} from "./verse.js";
import {$book} from "./book.js";

export const $reference = queryParam('reference', "JHN 1");

export const $referenceData = computed([
    $chapter,
    $book,
    $verse_start,
    $verse_end
], (chapter, book, verse_start, verse_end) => {
    return {
        chapter: chapter,
        book: book.name,
        book_id: book.book_id,
        verse_start: verse_start,
        verse_end: verse_end
    }
});

export const $visitReference = (reference) => {
    $reference.set(reference)
}
