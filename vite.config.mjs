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
            ],

            // Refreshes when a change is made to resource/view files or routes
            refresh: true,
        }),
        vue(),
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
