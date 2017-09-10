let mix = require('laravel-mix');

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

mix.options({ 
    processCssUrls: false 
});
//    .disableNotifications();

mix.react('resources/assets/js/app.js', 'public/assets/js')
   .copy('resources/assets/libs/fontawesome/fonts', 'public/assets/fonts')
   .sass('resources/assets/sass/app.scss', 'public/assets/css');

mix.browserSync({
    proxy: 'dev35.pcb.local',
    files: ['public/**/*.css', 'resources/**/*']
});