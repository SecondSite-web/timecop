"use strict";
// Gulpfile Taskrunner | by SecondSite
// Read the README.md for a list of the functions available
// Load plugins
const autoprefixer = require("autoprefixer"),
    // cleanCSS = require("gulp-clean-css"), // CSSNANO tested faster, not in use
    del = require("del"),
    gulp = require("gulp"),
    newer = require('gulp-newer'),
    cache = require('gulp-cache'),
    header = require("gulp-header"),
    merge = require("merge-stream"),
    plumber = require("gulp-plumber"),
    rename = require("gulp-rename"),
    sass = require("gulp-sass")(require('sass')),
    cssnano = require('cssnano'),
    postcss = require('gulp-postcss'),
    terser = require('gulp-terser-js'), // in use, tested better compression than Uglify
    uglify = require("gulp-uglify"), // Error rates higher than Terser
    concat = require('gulp-concat'),
    deporder = require('gulp-deporder'),
    stripdebug = require('gulp-strip-debug'),
    strip = require('gulp-strip-comments'),
    zip = require('gulp-zip'),
    pkg = require('./package.json'),
    mjml = require('gulp-mjml'),
    pipeline = require('readable-stream').pipeline;
sass.compiler = require('sass'); // node-sass compiler
// Javascript files to be concatenated and minified into admin.js, admin.min,js and map for backend facing pages
const adminScripts = [
    "./src/admin/js/js-compile/**/*"
];
const clientScripts = [
    "./src/client/js/**/*"
];
// Set the banner content for js and css files
const banner = ['/*!\n',
    ' * Dash PHP Template - <%= pkg.title %> v<%= pkg.version %> ((https://github.com/SecondSite-web/<%= pkg.name %>)\n',
    ' * Copyright ' + (new Date()).getFullYear(), ' <%= pkg.author %>\n',
    ' * Licensed under <%= pkg.license %> (https://github.com/SecondSite-web/<%= pkg.name %>/blob/master/LICENSE)\n',
    ' */\n',
    '\n'
].join('');

function clean()
{
    return del([
        './admin/css/',
        './admin/js/',
        './client/css/',
        './client/js/',
    ]);
}

// Bring third party dependencies from node_modules into vendor directory - part of 'gulp vendor' command
function modules()
{
    var bootstrapJS = gulp.src('./node_modules/bootstrap/dist/js/**/*')
            .pipe(gulp.dest('./vendor/bootstrap/js')),
        bootstrapSCSS = gulp.src('./node_modules/bootstrap/scss/**/*')
            .pipe(gulp.dest('./vendor/bootstrap/scss')),
        jquery = gulp.src('./node_modules/jquery/dist/*')
            .pipe(gulp.dest('./vendor/jquery')),
        jqueryValidation = gulp.src('./node_modules/jquery-validation/dist/**/*')
            .pipe(gulp.dest('./vendor/jquery-validation')),
        fa_fonts = gulp.src('./node_modules/@fortawesome/fontawesome-free/webfonts/**/*')
            .pipe(gulp.dest('./webfonts')),
        fa_admin_fonts = gulp.src('./node_modules/@fortawesome/fontawesome-free/webfonts/**/*')
            .pipe(gulp.dest('./admin/webfonts')),
        fa_elements = gulp.src('./node_modules/@fortawesome/fontawesome-free/scss/*')
            .pipe(gulp.dest('./vendor/fontawesome/scss')),
        datepicker = gulp.src('./node_modules/bootstrap-datepicker/dist/**/*')
            .pipe(gulp.dest('./vendor/datepicker')),
        dataTables = gulp.src([
            './node_modules/datatables.net/js/*.js',
            './node_modules/datatables.net-bs5/js/*.js',
            './node_modules/datatables.net-bs5/css/*.css',
            './node_modules/datatables.net-buttons/js/*.js',
            './node_modules/jszip/dist/*.js'
        ])
            .pipe(gulp.dest('./vendor/datatables')),
        jsZip = gulp.src([
            './node_modules/jszip/dist/*.js',
        ])
            .pipe(gulp.dest('./vendor/jszip'));
    return merge(bootstrapJS, bootstrapSCSS, jquery, jqueryValidation, fa_fonts, fa_admin_fonts, fa_elements, datepicker, dataTables, jsZip);
}
// writes font end css from scss - `gulp watch`
function adminCss()
{
    return mincss('./src/admin/scss/styles.scss', './admin/css/');
}
function clientCss()
{
    return mincss('./src/client/scss/styles.scss', './client/css/');
}

// concatenates and minifies backend JS - `gulp watch`
function adminCompile()
{
    return minjs(adminScripts, 'admin.js', './admin/js/');
}
function clientCompile()
{
    return minjs(clientScripts, 'client.js', './client/js/');
}
function jsSingle()
{
    return pagejs('./src/admin/js/js-single/*.js','./admin/js/');
}
// edit this function to change the way SCSS is minified into css - very important function
function mincss(source, destination)
{
    return gulp
        .src(source)
        .pipe(
            plumber({
                errorHandler: function (err) {
                    console.log(err);
                    this.emit('end');
                }
            })
        )
        // .pipe(sourcemaps.init()) // begins the sourcemap recording
        // records errors in the process and provides feedback
        .pipe(sass.sync({
            // fiber: Fiber,
            outputStyle: "expanded",
            includePaths: "./node_modules"
        }).on('error', sass.logError))
        // autoprefixer for backward compatibility
        .pipe(postcss([autoprefixer({
            overrideBrowserslist: ['last 2 versions'],
            cascade: false
        })]))
        // https://github.com/cssnano/cssnano - tested better than css-minify and clean-css and gulp-cssnano
        // tested far faster than gulp-cssnano
        .pipe(postcss([cssnano])) // cssnano minifier settings in package.json
        .pipe(header(banner, {pkg: pkg})) // writes the banner from 'gulfile.js' to the head of minified CSS files
        .pipe(rename({suffix: '.min'})) // adds .min. to minifed files
        // .pipe(sourcemaps.write('/')) // Output source maps.
        .pipe(gulp.dest(destination)); // writes to destination

}
// child function to minify js files
function minjs(input, filename, outputdir)
{
    return gulp.src(input)
        .pipe(concat(filename))
        .pipe(gulp.dest(outputdir))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest(outputdir));

    /*
    return pipeline(
        gulp.src(input),
        // sourcemaps.init(), // Begins sourcemap capture
        concat(filename), // Concatenates js files in input list
        // stripdebug(), // Strips all debug rules from js files
        // strip(), // Removes all comments from js files
        // terser(), // Supported fork of uglify.js that minifies js files - https://www.npmjs.com/package/terser
        // header(banner, {pkg: pkg}), // writes the banner from 'gulfile.js' to the head of minified JS files
        // rename({suffix:'.min'}),
        // sourcemaps.write('./'), // outputs the sourcemap
        gulp.dest(outputdir) // writes the files to destination
    );
    */
}
function pagejs(input, outputdir)
{
    return gulp.src(input)
        .pipe(gulp.dest(outputdir))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest(outputdir));

    /*
    return pipeline(
        gulp.src(input),
        // sourcemaps.init(), // Begins sourcemap capture
        concat(filename), // Concatenates js files in input list
        // stripdebug(), // Strips all debug rules from js files
        // strip(), // Removes all comments from js files
        // terser(), // Supported fork of uglify.js that minifies js files - https://www.npmjs.com/package/terser
        // header(banner, {pkg: pkg}), // writes the banner from 'gulfile.js' to the head of minified JS files
        // rename({suffix:'.min'}),
        // sourcemaps.write('./'), // outputs the sourcemap
        gulp.dest(outputdir) // writes the files to destination
    );
    */
}

// exports the full app into a zip file
function dist()
{
    return gulp.src([
        '**',
        '!**/node_modules/**',
        '!**/tests/**',
        '!/src/**',
        '!**/include/tests/**',
        '!./gitignore',
        '!./composer.json',
        '!./composer.lock',
        '!./gulpfile.js',
        '!./package.json',
        '!./package-lock.json',
        '!./phpstan.neon',
        '!./php_cs.cache'
    ])
        .pipe(zip('dash-dist.zip'))
        .pipe(gulp.dest('./dist'));
}
function zipFile()
{
    return gulp.src(['**',])
        // .pipe(zip('dash-master.zip'))
        .pipe(gulp.dest('../DASH'));
}

// removes compressed images from ./src (part of `gulp watch` or `gulp images` command
function img_cleanup()
{
    return del('./src/img/**/*');
}

function mailTemplate()
{
    return gulp.src('./src/email-templates/*.mjml')
        .pipe(mjml())
        .pipe(gulp.dest('./admin/email-templates'))
}

// Cleans working folders
// Watch files - any file change in a watched folder triggers the related compile function
function watchFiles()
{
    gulp.watch("./src/admin/scss/**/*", adminCss);
    gulp.watch("./src/client/scss/**/*", clientCss);
    gulp.watch(["./src/admin/js/js-compile/**/*", "!./src/admin/js/js-compile/**/*.min.js"], adminCompile);
    gulp.watch(["./src/admin/js/js-single/**/*", "!./src/admin/js/js-single/**/*.min.js"], jsSingle);
    gulp.watch(["./src/client/js/**/*", "!./src/admin/js/**/*.min.js"], clientCompile);
}

// Define complex tasks
const vendor = gulp.series(clean, modules);
const build = gulp.series(vendor, adminCss, clientCss, adminCompile, clientCompile, jsSingle);
const watch = gulp.parallel(watchFiles);

// Export tasks - tasks that can be run by 'gulp ' - eg. 'gulp images'
exports.mailTemplate = mailTemplate;
exports.adminCss = adminCss; // writes client css
exports.clientCss = clientCss; // writes client css
exports.adminCompile = adminCompile; // writes admin JS
exports.clientCompile = clientCompile;
exports.jsSingle = jsSingle;
exports.clean = clean; // deletes folders when doing a fresh compile or update
exports.vendor = vendor; // re-writes the ./vendor folder
exports.build = build; // does a full write of all css and js and images and email templates - A full 'gulp watch' cycle
exports.watch = watch; // Watches ./src and ./twig/src for all saved changes and auto compiles css, js, email templates, and images
exports.default = build; // sets - 'gulp' command to run the 'build' function
exports.zipFile = zipFile; // Exports the full file structure to a zip file in ./dist
exports.dist = dist;