var gulp = require('gulp');
var phpunit = require('gulp-phpunit');
var argv = require('yargs').argv;

gulp.task('test', function() {
    gulp.src('tests/**/*.php')
        .pipe(phpunit('', {
            notify: true,
            clear: true,
            noCoverage: true,
            coverageHtml: 'coverage',
            testClass: argv.class,
            testSuite: argv.suite
        }))
        .on('error', function() {

        });
});

gulp.task('watch', function() {
    gulp.watch(['tests/**/*.php', 'src/**/*.php'], ['test']);
});

gulp.task('default', ['test', 'watch']);
