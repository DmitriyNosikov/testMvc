const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');

let paths = {
  css: './core/blocks/**/*.css',
  sass: ['./core/blocks/**/*.sass', './core/blocks/**/*.scss'], //или './core/blocks/**/*.{sass,scss}'
  js: '',
  dest: './core/blocks/'
};

gulp.task('default', ['watch']);

gulp.task('sass', function () {
  return gulp.src(paths.sass)
  .pipe(sass({outputStyle: "expanded"}).on("error", sass.logError))
  .pipe(gulp.dest(paths.dest));
});

gulp.task('set-prefix', function(){
  return gulp.src(paths.css)
  .pipe(autoprefixer({
    browsers: ['last 2 versions'],
  }))
  .pipe(gulp.dest(paths.dest))
});

gulp.task('watch', ['sass', 'set-prefix'], function () {
  gulp.watch(paths.sass, ['sass']);
});