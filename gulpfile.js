var gulp = require('gulp'),
    sass = require('gulp-sass'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    minifyCss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    rename = require('gulp-rename'),
    pngquant = require('imagemin-pngquant'),
    purify = require('gulp-purifycss');


var css_files = [
    './resources/assets/css/vendor/bootstrap.min.css',
    './resources/assets/css/vendor/flag-icon.css',
    './resources/assets/css/vendor/jquery.datetimepicker.css',
    './resources/assets/css/vendor/jquery.jscrollpane.css',
    './resources/assets/css/vendor/jquery-ui-1.10.4.tooltip.css',
    './resources/assets/css/vendor/leaflet.css',
    './resources/assets/css/vendor/select2.min.css',
    './resources/assets/css/vendor/jquery.dataTables.min.css',
    './resources/assets/css/vendor/intro.min.css',
    './resources/assets/css/app.css'
];

var css_style = [
    './resources/assets/css/style.css'
];

var js_files = [
    './resources/assets/js/vendor/jquery.js',
    './resources/assets/js/vendor/bootstrap.min.js',
    './resources/assets/js/vendor/modernizr.js',
    './resources/assets/js/vendor/jquery-ui-1.10.4.tooltip.js',
    './resources/assets/js/vendor/jquery.cookie.js',
    './resources/assets/js/vendor/jquery.mousewheel.js',
    './resources/assets/js/vendor/jquery.jscrollpane.min.js',
    './resources/assets/js/vendor/select2.min.js',
    './resources/assets/js/vendor/jquery.datetimepicker.full.min.js',
    './resources/assets/js/vendor/jquery.dataTables.min.js',
    './resources/assets/js/vendor/ga.js',
    './resources/assets/js/vendor/intro.min.js',
    './resources/assets/js/vendor/jquery.validate.js',
    './resources/assets/js/script.js'
];


/**
 * Compile files from _scss
 */
gulp.task('sass', function () {
    return gulp.src('./resources/assets/css/app.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(postcss([autoprefixer({browsers: ['last 30 versions', '> 1%', 'ie 8', 'ie 7']})]))
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./resources/assets/css'));
});

/*
 * Watch scss files for changes & recompile
 */
gulp.task('watch', function () {
    gulp.watch('./resources/assets/app.scss', ['sass']);
    gulp.watch(css_files, ['css-main']);
    gulp.watch(css_style, ['css-style']);
    gulp.watch(js_files, ['js-main']);
    gulp.watch(['image-min']);
});

gulp.task('css-main', function () {
    return gulp.src(css_files)
        .pipe(sourcemaps.init())
        .pipe(concat('main.css'))
        .pipe(minifyCss({compatibility: 'ie8'}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./resources/assets/css'));
});

//remove unused css

gulp.task('purify-css', function() {
    return gulp.src('./resources/assets/css/main.min.css')
        .pipe(purify(['./resources/views/**/*.blade.php','./public/js/*.js']))
        .pipe(minifyCss({compatibility: 'ie8'}))
        .pipe(gulp.dest('./public/css'));
});

/*
 * Default task, running just `gulp` will compile the sass,
 */
gulp.task('default', ['sass', 'watch', 'css-main']);

gulp.task('css-style', function () {
    return gulp.src(css_style)
        .pipe(sourcemaps.init())
        .pipe(rename({suffix: '.min'}))
        .pipe(minifyCss({compatibility: 'ie8'}))
        .pipe(gulp.dest('./public/css'));
});

gulp.task('js-main', function () {
    return gulp.src(js_files)
        .pipe(concat('main.js'))
        .pipe(gulp.dest('./public/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'));
});


gulp.task('image-min', function() {
    return gulp.src('./public/img/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('./public/images'));
});