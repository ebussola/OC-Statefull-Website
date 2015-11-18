var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

gulp.task('default', function() {
    return gulp.src('assets/js/ajax-flash-message.js')
        .pipe(uglify())
        .pipe(rename('ajax-flash-message.min.js'))
        .pipe(gulp.dest('assets/js'));
});