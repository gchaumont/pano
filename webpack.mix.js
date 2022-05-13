const mix = require('laravel-mix');

require('laravel-mix-bundle-analyzer');

const { CleanWebpackPlugin } = require('clean-webpack-plugin');
let child_process = require('child_process');

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


mix.js('resources/js/app.js', 'public/app.js')
    .sass('resources/sass/app.scss', 'public/app.css')
    .vue({ runtimeOnly: true })
    // .version()




