import {atom} from 'nanostores'

const $reference = atom("Genesis 1:1")

export const $resetReference = () => {
    $reference.set("Genesis 1:1")
}

export const $getReferenceRoute = () => {
    return $reference.get().replace(" ", "-")
}

export const $setReferenceRoute = (route) => {
    console.log(route)
    $reference.set(route.replace("-", " "))
}

export default $reference
