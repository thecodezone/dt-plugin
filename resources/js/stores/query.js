// store/posts.ts
import {fetchState} from '../nanostore/fetch-state.js';
import {$bible, $bibleAbbr} from "./bible.js";
import {$book, $bookId} from "./book.js";
import {$chapter} from "./chapter.js";
import {$content} from "./content.js";
import {$reference} from './reference.js';
import {$language, $languageId, $languageIso} from "./language.js";
import {$media_type, $media_type_key} from "./media-type.js";
import {apiUrl} from "../helpers.js";

export const $query = fetchState([apiUrl('scripture'), '?reference=', $reference]);

$query.listen(({loading, data}) => {
    if (loading) {
        return;
    }
    console.log(data);
    $bible.set(data.bible ?? {});
    $bibleAbbr.set(data.bible.abbr ?? '');
    $book.set(data.book ?? {});
    $bookId.set(data.book.book_id ?? '');
    $chapter.set(data.chapter ?? '');
    $content.set(data.content);
    $language.set(data.language);
    $languageId.set(data.language.id);
    $languageIso.set(data.language.iso);
    $media_type.set(data.media_type);
    $media_type_key.set(data.media_type.key);
});