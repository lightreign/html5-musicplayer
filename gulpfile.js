/*
  Project Gulp file
  Author: Adrian Pennington
*/

'use strict';

let gulp = require('gulp'),
  csso = require('gulp-csso'),
  rename = require("gulp-rename"),
  uglify = require('gulp-uglify'),
  concat = require('gulp-concat'),
  sourcemaps = require('gulp-sourcemaps'),
  del = require('del'),
  noop = require('gulp-noop');

const mode = process.env.MODE;

// Clean
gulp.task('clean', () => del(['./public/assets/js','./public/assets/css', './public/assets/fonts', './public/assets/themes']));

// Add CSS
gulp.task('css', function() {
  return gulp.src([
      './node_modules/bootstrap/dist/css/bootstrap.css',
      './node_modules/jquery-ui-dist/jquery-ui.min.css',
      './node_modules/jquery-contextmenu/dist/jquery.contextMenu.css',
      './resources/css/layout.css'
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('bundle.css'))
    .pipe(sourcemaps.write())
    .pipe(csso())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('./public/assets/css'))
});

// Add Fonts
gulp.task('fonts', function() {
  return gulp.src(['./node_modules/bootstrap/dist/fonts/*'])
    .pipe(gulp.dest('./public/assets/fonts'))
});

// Add themes
gulp.task('themes', function() {
  return gulp.src(['./resources/themes/*/*'])
    .pipe(gulp.dest('./public/assets/themes'))
});

// Add Images
gulp.task('images', function() {
  return gulp.src(['./node_modules/jquery-ui-dist/images/*'])
    .pipe(gulp.dest('./public/assets/css/images'))
});

// Add scripts
gulp.task('scripts', function() {
  return gulp.src([
    './node_modules/jquery/dist/jquery.js',
    './node_modules/jquery-ui-dist/jquery-ui.js',
    './node_modules/bootstrap/dist/js/bootstrap.js',
    './node_modules/lodash/lodash.js',
    './node_modules/jquery-contextmenu/dist/jquery.contextMenu.js',
    './resources/js/*.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('bundle.js'))
    .pipe(sourcemaps.write())
    .pipe( mode === 'dev' ? noop() : uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('./public/assets/js'))
});

// Gulp task to minify all files
gulp.task('default', gulp.series('clean', 'css', 'fonts', 'themes', 'images', 'scripts'));

// Watch time
gulp.task('watch', function() {
  gulp.watch('./resources/css/*.css', gulp.series('css'));
  gulp.watch('./resources/themes/*/*', gulp.series('themes'));
  gulp.watch('./resources/js/*.js', gulp.series('scripts'));
});



