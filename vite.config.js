import {v4wp} from '@kucrut/vite-for-wp';
import babel from 'vite-plugin-babel';
import svgLoader from "vite-svg-loader";

export default {
    plugins: [
        v4wp({
            input: {
                'plugin': 'resources/js/plugin.js',
                'admin': 'resources/js/admin.js',
            },
            outDir: 'dist', // Optional, defaults to 'dist'.
        }),
        babel(),
        svgLoader({
            defaultImport: 'raw'
        }),
    ]
};