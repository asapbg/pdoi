const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .copy('node_modules/admin-lte/dist/img', 'public/dist/img')
    .sass('resources/sass/app.scss', 'public/css');

mix.scripts([
    'resources/adminlte-3.2/plugins/jquery/jquery.min.js',
    'resources/adminlte-3.2/plugins/jquery-ui/jquery-ui.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-5/js/bootstrap.bundle.min.js',
    // 'resources/adminlte-3.2/plugins/bootstrap-slider/bootstrap-slider.js',
    // 'resources/adminlte-3.2/plugins/iCheck/icheck.min.js',
    'resources/adminlte-3.2/plugins/print-this/printThis.js',
    'resources/adminlte-3.2/plugins/toastr/toastr.min.js',
    'resources/adminlte-3.2/plugins/fancybox/fancybox-4.0.min.js',
    'resources/adminlte-3.2/plugins/summernote/summernote.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js',
    'resources/adminlte-3.2/plugins/datatables-bs5/datatables.min.js',
    'resources/adminlte-3.2/plugins/select2/js/select2.full.min.js',
    'resources/adminlte-3.2/plugins/select2/js/i18n/bg.js',
    'resources/adminlte-3.2/plugins/moment/moment.min.js',
    'resources/adminlte-3.2/plugins/daterangepicker/daterangepicker.js',
    'resources/adminlte-3.2/dist/js/adminlte.min.js',
    'resources/js/main.js'
], 'public/js/admin.js');


mix.styles([
    'resources/adminlte-3.2/plugins/jquery-ui/jquery-ui.min.css',
    'resources/adminlte-3.2/plugins/bootstrap-5/css/bootstrap.min.css',
    'resources/adminlte-3.2/plugins/fontawesome-free/css/all.min.css',
    // 'resources/adminlte-3.2/plugins/bootstrap-slider/css/bootstrap-slider.min.css',
    'resources/adminlte-3.2/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    'resources/adminlte-3.2/plugins/toastr/toastr.min.css',
    'resources/adminlte-3.2/plugins/fancybox/fancybox-4.0.min.css',
    'resources/adminlte-3.2/plugins/summernote/summernote.min.css',
    'resources/adminlte-3.2/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css',
    'resources/adminlte-3.2/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css',
    'resources/adminlte-3.2/plugins/datatables-bs5/datatables.min.css',
    'resources/adminlte-3.2/plugins/select2/css/select2.min.css',
    'resources/adminlte-3.2/plugins/daterangepicker/daterangepicker.css',
    'resources/adminlte-3.2/dist/css/adminlte.css',
    'resources/adminlte-3.2/dist/css/main.css',
    'resources/css/admin.css'
], 'public/css/admin.css');
