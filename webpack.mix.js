const mix = require('laravel-mix');

const tailwindcss = require('tailwindcss'); /* Add this line at the top */

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


mix.js('resources/js/app.js', 'public')
    .sass('resources/sass/app.scss', '')
   .options({
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .setPublicPath('public')
    .vue({ runtimeOnly: true })
    .version()