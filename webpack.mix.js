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
    .extract(['bootstrap'], 'assets/admin/js/admin-vendor.js')
    .typeScript('resources/js/app.ts', 'public/assets/js')
    .sass('resources/sass/app.scss', 'public/assets/css')
    .sass('resources/sass/v2/app-v2.scss', 'public/assets/css')
    .sass('resources/sass/admin/admin.scss', 'public/assets/admin/css')
    .options({
        processCssUrls: false
    })
   .sass('resources/sass/navonly.scss', 'public/assets/css')
   .extract([
        'vue'
    ]);

if(mix.config.production) {
    mix.version();

} else {
    mix.browserSync({
        open: false,
        proxy: 'laravel.test',
        files: [
            'resources/**/*.php',
            'resources/**/*.css',
            'resources/**/*.js',
            'resources/**/*.ts',
            'public/**/*',
        ]
    });
}
