import {v4wp} from '@kucrut/vite-for-wp';
import babel from 'vite-plugin-babel';

export default {
    plugins: [
        v4wp({
            input: 'resources/js/plugin.js', // Optional, defaults to 'src/main.js'.
            outDir: 'dist', // Optional, defaults to 'dist'.
        }),
        babel(),
    ],
};