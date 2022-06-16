const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.typeScript('resources/js/admin/admin.ts', 'public/assets/admin/js')
    .extract(['bootstrap', 'jquery', '@popperjs/core', 'choices.js'], 'public/assets/admin/js/admin-vendor.js')
    .sass('resources/sass/admin/admin-dark.scss', 'public/assets/admin/css')
    .sass('resources/sass/admin/admin-light.scss', 'public/assets/admin/css');

mix.typeScript('resources/js/app.ts', 'public/assets/js')
    .sass('resources/sass/v2/app-v2.scss', 'public/assets/css')
    .vue()
    .options({
        processCssUrls: false
    })
    .extract([
        'vue',
    ]);

mix.webpackConfig({
    stats: {
        children: true
    }
});

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();

    mix.browserSync({
        open: false,
        proxy: 'laravel.test',
        files: [
            'resources/views/**/*',
            'resources/sass/**/*.css',
            'resources/js/**/*.js',
            'resources/js/**/*.ts',
            'public/assets/fonts/**/*',
            'public/assets/images/**/*',
        ]
    });
}
