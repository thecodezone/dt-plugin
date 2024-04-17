import {nanoquery} from '@nanostores/query';

export const [fetchState, mutateResponse] = nanoquery({
    fetcher: (...keys) => fetch(keys.join(''))
        .then((r) => r.json())
        .catch((r) => r.json()),
});