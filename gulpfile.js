var gulp = require('gulp');
const {phpMinify} = require('@cedx/gulp-php-minify');
var concat = require('gulp-concat');
var deleteLines = require('gulp-delete-lines');
var insert = require('gulp-insert');

gulp.task('default', function() {
    gulp
        .src([
            './functions.php',
            './[A-Z]*.php',
            './index.php'
        ])
        .pipe(phpMinify())
        .pipe(concat('dump.php'))
        .pipe(deleteLines({
            filters: [
                /<\?php/
            ]
        }))
        .pipe(insert.prepend('<?php '))
        .pipe(insert.append('\n'))
        .pipe(gulp.dest('./dump/'))
    ;
});
