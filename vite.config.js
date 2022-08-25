import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/front/front.scss',
                'resources/js/app.ts',
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
            '@': '/resources/js', // TODO: remove this after migrating mix
        }
    },
});



// mix.typeScript('resources/js/admin/admin.ts', 'public/assets/admin/js')
//     .extract(['bootstrap', 'jquery', '@popperjs/core', 'choices.js'], 'public/assets/admin/js/admin-vendor.js')
//     .sass('resources/sass/admin/admin-dark.scss', 'public/assets/admin/css')
//     .sass('resources/sass/admin/admin-light.scss', 'public/assets/admin/css');
//
// mix.typeScript('resources/js/app.ts', 'public/assets/js')
//     .sass('resources/sass/v2/app-v2.scss', 'public/assets/css')
//     .vue()
//     .options({
//         processCssUrls: false
//     })
//     .extract([
//         'vue',
//     ]);
//
// mix.webpackConfig({
//     stats: {
//         children: true
//     }
// });
//
// if (mix.inProduction()) {
//     mix.version();
// } else {
//     mix.sourceMaps();
//
//     mix.browserSync({
//         open: false,
//         proxy: 'laravel.test',
//         files: [
//             'resources/views/**/*',
//             'resources/sass/**/*.css',
//             'resources/js/**/*.js',
//             'resources/js/**/*.ts',
//             'public/assets/fonts/**/*',
//             'public/assets/images/**/*',
//         ]
//     });
// }
