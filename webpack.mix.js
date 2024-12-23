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
    'resources/js/partials/select2Ekatte.js',
    'resources/js/partials/uploadFileToTable.js',
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
    'resources/adminlte-3.2/dist/css/alt/adminlte.plugins.css',
    'resources/adminlte-3.2/dist/css/main.css',
    'resources/css/admin.css'
], 'public/css/admin.css');

mix.scripts([
    // 'resources/js/front/jquery-3.7.0.js',
    'resources/js/front/jquery-3.6.3.min.js',
    'resources/js/front/jquery_ui-1.13.2.min.js',
    // 'resources/js/front/bootstrap-5.3/bootstrap.min.js',
    'resources/js/front/bootstrap-5.3/bootstrap.bundle.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js',
    'resources/adminlte-3.2/plugins/bootstrap-datepicker/bootstrap-datepicker.bg.min.js',
    'resources/js/front/select2.full.min.js',
    'resources/js/front/app.js',
    'resources/js/front/apply_application.js',
    'resources/js/front/renew_application.js',
    'resources/js/partials/select2Ekatte.js',
    'resources/adminlte-3.2/plugins/daterangepicker/daterangepicker.js',
], 'public/js/app.js');

mix.styles([
    'resources/css/front/jquery_ui-1.13.2.min.css',
    'resources/css/front/bootstrap-5.3/bootstrap.min.css',
    'resources/adminlte-3.2/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css',
    'resources/adminlte-3.2/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css',
    'resources/css/front/select2.min.css',
    'resources/css/app.css'
], 'public/css/app.css');
