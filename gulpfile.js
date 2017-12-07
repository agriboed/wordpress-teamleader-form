const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
const sass = require('gulp-sass');
const clean = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const babel = require('gulp-babel');
const watch = require('gulp-watch');

gulp.task('default', () => {
  gulp.src('./assets/css/*')
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(clean())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./teamleader/assets/css/'));

  gulp.src(['./assets/js/*.js', '!./assets/js/vendor/**/*.js'])
    .pipe(sourcemaps.init())
    .pipe(babel({
      presets: ['env'],
    }))
    .pipe(concat('app.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./teamleader/assets/js'));

  gulp.src('./assets/image/**/*')
    .pipe(imagemin())
    .pipe(gulp.dest('./teamleader/assets/image'));
});

gulp.task('watch', () => {
  gulp.watch(['./assets/js/*.js', '!./assets/js/vendor/**/*.js', './assets/css/*'], ['default']);
});
