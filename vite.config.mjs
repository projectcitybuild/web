import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/front/front.scss',
                'resources/js/front/front.ts',
                'resources/js/manage/manage.ts',
                'resources/sass/manage.scss',
                'resources/js/review/review.ts',
                'resources/sass/review.scss',
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
    server: {
        // `npm run dev` will not route to the correct IP address without this
        // https://github.com/laravel/vite-plugin/issues/28#issuecomment-1169592126
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        // Assets smaller than 4kb are inlined as base64, however there is currently no
        // support for using it automatically in blade files.
        // To get around that, we'll disable inlining.
        // https://stackoverflow.com/questions/73502963/larave-vite-vite-manifest-missing-odd-files
        assetsInlineLimit: 0
    }
});
