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

mix.react('app/Resources/assets/js/app.js', 'public/assets/js')
   .sass('app/Resources/assets/sass/app.scss', 'public/assets/css');

mix.browserSync({
    proxy: 'dev35.pcb.local',
    files: ['public/**/*.css', 'app/Resources/**/*']
});