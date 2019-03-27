const mix_ = require('laravel-mix');


mix_.setPublicPath('./dist')
  .js('./assets/js/_main.js', 'js/main.min.js')
  .sass('./assets/sass/main.scss', 'css/main.min.css')
  .options({
    processCssUrls: false
  })
  .copy('./assets/img/*', 'dist/images/');

mix_.browserSync({
  proxy: 'ccrc.docker'
});

if (mix_.inProduction()) {
  mix_.version();
} else {
  mix_.sourceMaps();
}
