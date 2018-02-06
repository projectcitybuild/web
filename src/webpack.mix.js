let mix = require('laravel-mix');


// there seems to be a bug causing compile time to skyrocket when css url 
// processing is enabled. re-enable this when it's fixed
mix.options({ 
    processCssUrls: false 
});

// skip node_modules because we don't want to compile any vendor's typescript
mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
                exclude: /node_modules/,
            },
        ],
    },
    resolve: {
        extensions: ['*', '.js', '.jsx', '.ts', '.tsx'],
    },
});

mix.ts('app/Resources/assets/js/app.ts', 'public/assets/js')
   .sass('app/Resources/assets/sass/app.scss', 'public/assets/css')
   .version();

mix.browserSync({
    proxy: '192.168.99.100',
    files: ['public/**/*.css', 'app/Resources/**/*']
});