export const reference_from_content = (items = []) => {
    if (!items || !items.length) return ""
    const firstItem = items[0]
    const book = firstItem.book_name
    const lastItem = items[items.length - 1]
    const firstVerse = firstItem.verse_start ?? firstItem.verse ?? null
    const lastVerse = lastItem.verse_end ?? lastItem.verse ?? null
    const firstChapter = firstItem.chapter_start ?? firstItem.chapter ?? null
    const lastChapter = lastItem.chapter_end ?? lastItem.chapter ?? null

    //Single full chapter
    if (((firstChapter && !lastChapter) || (firstChapter === lastChapter)) && firstVerse && !lastVerse) {
        return `${book} ${firstChapter}`
    }

    //Multiple full chapters
    if (firstChapter && lastChapter && firstVerse === 1 && !lastVerse) {
        if (firstChapter === lastChapter) {
            if (firstVerse && lastVerse) {
                if (firstVerse === lastVerse) {
                    return `${firstChapter}:${firstVerse}`
                }
                return `${firstChapter}:${firstVerse}-${lastVerse}`
            }
            return `${firstChapter}`
        }
        return `${book} ${firstChapter}-${lastChapter}`
    }

    //Single partial chapter
    if (firstChapter && lastChapter && firstChapter === lastChapter && firstVerse && lastVerse) {
        if (firstVerse === lastVerse) {
            return `${book} ${firstChapter}:${firstVerse}`
        }
        return `${book} ${firstChapter}:${firstVerse}-${lastVerse}`
    }

    //Multiple partial chapters
    if (firstChapter && lastChapter && firstVerse && lastVerse) {
        if (firstChapter === lastChapter) {
            if (firstVerse === lastVerse) {
                return `${book} ${firstChapter}:${firstVerse}`
            }
            return `${book} ${firstChapter}:${firstVerse}-${lastVerse}`
        }
        return `${book} ${firstChapter}:${firstVerse}-${lastChapter}:${lastVerse}`
    }

    //Single verse
    if (firstVerse && lastVerse && firstVerse === lastVerse) {
        return `${book} ${firstChapter}:${firstVerse}`
    }

    //Multiple verses
    if (firstVerse && lastVerse) {
        return `${book} ${firstChapter}:${firstVerse}-${lastVerse}`
    }

    return `${book} ${firstChapter}`
}