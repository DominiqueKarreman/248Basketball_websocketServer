const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .css('resources/css/helper.css', 'public/css/helper.css')
    .css('resources/css/ws.css', 'public/css')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.options({
    hmrOptions: {
        host: '116.203.134.102',
        port: 8080,
    }
})
