var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', function () {
  return gulp.src(['./core/blocks/**/*sass', './core/blocks/**/*scss'])
    .pipe(sass({outputStyle: "expanded"}).on("error", sass.logError))
    .pipe(gulp.dest('./core/blocks/'));
});
 
gulp.task('set-prefix', function(){
	gulp.src('./core/blocks/**/*css')
        .pipe(autoprefixer({
            browsers: ['last 20 versions'],
        }))
        .pipe(gulp.dest('./core/blocks/'))
});

gulp.task('watch', ['sass', 'set-prefix'], function () {
  gulp.watch(['./core/blocks/**/*.scss', './core/blocks/**/*.sass'], ['sass']);
});