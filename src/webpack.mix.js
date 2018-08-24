let mix = require('laravel-mix');

mix.setPublicPath('interfaces/web/public');

mix.typeScript('interfaces/web/assets/js/app.tsx', 'interfaces/web/public/assets/js')
   .sass('interfaces/web/assets/sass/app.scss', 'interfaces/web/public/assets/css')
   .sass('interfaces/web/assets/sass/navonly.scss', 'interfaces/web/public/assets/css')
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
            'interfaces/web/assets/**/*.css', 
            'interfaces/web/assets/**/*.js',    
            'interfaces/web/assets/**/*.ts',    
        ]
    });
}