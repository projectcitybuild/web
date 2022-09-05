import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/front/front.scss',
                'resources/js/front/front.ts',
                'resources/sass/admin/admin.scss',
                'resources/js/admin/admin.ts',
            ],

            // Refreshes when a change is made to resource/view files or routes
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directly as expected.
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~normalize-scss': path.resolve(__dirname, 'node_modules/normalize-scss'),
            '~choices.js': path.resolve(__dirname, 'node_modules/choices.js'),
        }
    },
    server: {
        // `npm run dev` will not route to the correct IP address without this
        // https://github.com/laravel/vite-plugin/issues/28#issuecomment-1169592126
        hmr: {
            host: 'localhost',
        },
    }
});
