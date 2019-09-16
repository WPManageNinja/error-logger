let mix = require('./src/mix');

mix.js('src/app.js', 'assets/js/app.js');
mix.sass('src/scss/app.scss', 'assets/css/app.css');

mix.copy('node_modules/element-ui/lib/theme-chalk/fonts', 'assets/css/fonts');
