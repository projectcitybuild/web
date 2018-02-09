let mix = require('laravel-mix');


// there seems to be a bug causing compile time to skyrocket when css url 
// processing is enabled. re-enable this when it's fixed
// mix.options({ 
//     processCssUrls: false 
// });

mix.typeScript('assets/js/app.ts', 'public/assets/js')
   .sass('assets/sass/app.scss', 'public/assets/css')
   .version();

mix.browserSync({
    proxy: '192.168.99.100',
    files: [
        'assets/views/**/*.blade.php',
        'assets/lang/**/*.php',
        'public/**/*.css', 
        'public/**/*.js',    
    ]
});