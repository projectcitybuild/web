let mix = require('laravel-mix');


mix.typeScript('assets/js/app.tsx', 'public/assets/js')
   .sass('assets/sass/app.scss', 'public/assets/css')

if(mix.config.production) {
    console.log('test');
    mix.minify()
       .version();
}

else {
    mix.browserSync({
        proxy: '192.168.99.100',
        files: [
            'assets/**/*.php',
            'public/**/*.css', 
            'public/**/*.js',    
        ]
    });
}