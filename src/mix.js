const webpack = require('webpack');
let mix = require('laravel-mix');
mix.setPublicPath('assets');
mix.setResourceRoot('../');

/* global Mix path */
mix.webpackConfig({
    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.(js|vue)$/,
                loader: 'eslint-loader',
                exclude: /node_modules/
            }
        ]
    },
    output: {
        publicPath: Mix.isUsing('hmr') ? '/' : '/wp-content/plugins/error-logger/assets/',
        chunkFilename: 'js/[name].js'
    },
    plugins: [
        // Ignore all locale files of moment.js
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': path.resolve(__dirname, '../')
        }
    }
});

mix.options({ processCssUrls: false });

module.exports = mix;
