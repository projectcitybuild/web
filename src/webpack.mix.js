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

mix.typeScript('app/resources/js/app.tsx', 'app/public/assets/js')
   .sass('app/resources/sass/app.scss', 'app/public/assets/css')
   .sass('app/resources/sass/navonly.scss', 'app/public/assets/css')
   .extract([
        'react', 
        'date-fns', 
        'react-dom', 
        'axios'
    ]);

if(mix.config.production) {
    mix.version();

} else {
    mix.browserSync({
        proxy: 'nginx',
        open: false,
        files: [
            'app/**/*.php',
            'app/resources/**/*.css', 
            'app/resources/**/*.js',
            'app/resources/**/*.ts',
            'app/public/**/*',
        ]
    });
}