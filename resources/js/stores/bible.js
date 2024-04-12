import {atom, computed} from 'nanostores'


export const $bible = atom({})
export const $bibleAbbr = atom("")

export const $copyright = computed($bible, bible => bible?.mark)
