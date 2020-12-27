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

mix.sass('resources/sass/app.scss', 'public/assets/css')
    .options({
        processCssUrls: false
    })
   .sass('resources/sass/navonly.scss', 'public/assets/css');

if(mix.config.production) {
    mix.version();

} else {
    mix.browserSync({
        open: false,
        files: [
            'resources/**/*.php',
            'resources/**/*.css',
            'resources/**/*.js',
            'resources/**/*.ts',
            'public/**/*',
        ]
    });
}
