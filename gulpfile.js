var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

gulp.task('js', function () {
    gulp.src([
        'public/js/controllers/*.js'
        , 'public/js/services/*.js'
    ])
        .pipe(concat('eyex-local-production.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/compiled/'))
});

gulp.task('default', ['js']);