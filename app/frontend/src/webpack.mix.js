const mix = require('laravel-mix');
mix.ts('resources/js/app.ts', 'public/js')
    .css('resources/css/app.css', 'public/css').vue();
