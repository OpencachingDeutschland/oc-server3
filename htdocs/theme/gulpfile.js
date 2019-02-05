const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const cssmin = require('gulp-cssmin');
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const minify = require('gulp-minify');
const rename = require('gulp-rename');
const imagemin = require('gulp-imagemin');
const fs = require('fs');

const cssAddonsPath = './css/modules/';

// CSS Tasks
gulp.task('css-compile', function() {
  gulp.src('scss/*.scss')
    .pipe(sass({outputStyle: 'nested'}).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 10 versions'],
      cascade: false
    }))
    .pipe(gulp.dest('../web/assets/css/'));

    gulp.start('css-compile-modules');
});

// CSS Tasks
gulp.task('css-compile-modules', function() {
  gulp.src('scss/**/modules/**/*.scss')
    .pipe(sass({outputStyle: 'nested'}).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 10 versions'],
      cascade: false
    }))
    .pipe(rename({ dirname: cssAddonsPath }))
    .pipe(gulp.dest('../web/assets/'));
});


gulp.task('css-minify', function() {
    gulp.src(['../web/assets/css/*.css', '!../web/assets/css/*.min.css', '!../web/assets/css/bootstrap.css'])
      .pipe(cssmin())
      .pipe(rename({suffix: '.min'}))
      .pipe(gulp.dest('../web/assets/css'));

    gulp.start('css-minify-modules');
});

gulp.task('css-minify-modules', function() {
  gulp.src(['../web/assets/css/modules/*.css', '!../web/assets/css/modules/*.min.css'])
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('../web/assets/css/modules'));
});

// JavaScript Tasks
gulp.task('js-build', function() {

  const plugins = getJSModules();

  return gulp.src(plugins.modules)
    .pipe(concat('mdb.js'))
    .pipe(gulp.dest('../web/assets/js/'));

    gulp.start('js-lite-build');
    gulp.start('js-minify');

});

gulp.task('js-minify', function() {
  gulp.src(['../web/assets/js/mdb.js'])
    .pipe(minify({
      ext:{
        // src:'.js',
        min:'.min.js'
      },
      noSource: true,
    }))
    .pipe(gulp.dest('../web/assets/js'));
});

// Image Compression
gulp.task('img-compression', function() {
  gulp.src('./img/*')
    .pipe(imagemin([
      imagemin.gifsicle({interlaced: true}),
      imagemin.jpegtran({progressive: true}),
      imagemin.optipng({optimizationLevel: 5}),
      imagemin.svgo({
        plugins: [
          {removeViewBox: true},
          {cleanupIDs: false}
        ]
      })
    ]))
    .pipe(gulp.dest('../web/assets/img'));
});

// Live Server
gulp.task('live-server', function() {
  browserSync.init({
    server: {
      baseDir: "../web/assets",
      directory: true
    },
    notify: false
  });

  gulp.watch("**/*", {cwd: '../web/assets/'}, browserSync.reload);
});

// Watch on everything
gulp.task('mdb-go', function() {
  gulp.start('live-server');
  gulp.watch("scss/**/*.scss", ['css-compile']);
  gulp.watch(["dist/css/*.css", "!dist/css/*.min.css"], ['css-minify']);
  gulp.watch("js/**/*.js", ['js-build']);
  gulp.watch(["dist/js/*.js", "!dist/js/*.min.js"], ['js-minify']);
  gulp.watch("**/*", {cwd: './img/'}, ['img-compression']);
});

gulp.task('build', function() {
    gulp.start('copy');
    gulp.start('css-compile');
    gulp.start('css-minify');
    gulp.start('js-build');
    gulp.start('js-minify');
    gulp.start('img-compression');
});

gulp.task('build-dev', function() {
    gulp.start('copy');
    gulp.start('css-compile');
    gulp.start('css-minify');
    gulp.start('js-build');
    gulp.start('js-minify');
});

gulp.task('copy', function() {
    gulp.src('./css/*.css').pipe(gulp.dest('../web/assets/css'));
    gulp.src('./js/*.js').pipe(gulp.dest('../web/assets/js'));
    gulp.src('./font/roboto/*').pipe(gulp.dest('../web/assets/font/roboto'));
});

function getJSModules() {
  delete require.cache[require.resolve('./js/modules.js')];
  return require('./js/modules');
}
