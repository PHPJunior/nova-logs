let mix = require('laravel-mix')
let tailwindcss = require('tailwindcss')

require('./nova.mix')

mix
    .setPublicPath('dist')
    .js('resources/js/tool.js', 'js')
    .vue({ version: 3 })
    .postCss('resources/css/tool.css', 'css', [
        tailwindcss('tailwind.config.js'),
    ])
    .nova('php-junior/nova-logs')
