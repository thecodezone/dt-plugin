import {atom} from "nanostores"

export const $message = atom("")

export const $displayMessage = (msg) => {
    $message.set(msg)
    setTimeout(() => {
        $message.set("")
    }, 10000)
}