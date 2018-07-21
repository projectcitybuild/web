let mix = require('laravel-mix');


mix.typeScript('interfaces/web/assets/js/app.tsx', 'public/assets/js')
   .sass('interfaces/web/assets/sass/app.scss', 'public/assets/css')
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
        proxy: '192.168.99.100',
        files: [
            'interfaces/web/**/*.php',
            'public/**/*.css', 
            'public/**/*.js',    
        ]
    });
}