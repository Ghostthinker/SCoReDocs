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

const resourcesAssets = 'resources/assets/';
const dest = 'public/assets/';

mix.js('resources/js/app.js', 'public/js')
    .copy(`${resourcesAssets}images`, `${dest}images`, false)
    .sass('resources/sass/app.scss', 'public/css');
mix.version();
