<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\PdoiApplicationController as PdoiApplicationFrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//Route::get('/test', [\App\Http\Controllers\Test::class, 'index']);

Auth::routes(['verify' => true]);

Route::feeds();

include 'eauth.php';

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('front.logout');

Route::controller(\App\Http\Controllers\Auth\ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password',                'showLinkRequestForm')->name('forgot_pass');
    Route::post('/forgot-password/send',                'sendResetLinkEmail')->name('forgot_pass.password.send');
    Route::post('/forgot-password/update',                'confirmPassword')->name('forgot_pass.password.update');
});

Route::get('/locale', function (Request $request) {
    if ($request->has('locale')) {
        session(['locale' => $request->offsetGet('locale')]);
        app()->setLocale($request->offsetGet('locale'));
    }
    return back();
})->name('change-locale');

//pdoi subjects modal
Route::get('/get-pdoi-subjects', [CommonController::class, 'modalPdoiSubjects'])->name('modal.pdoi_subjects');
Route::get('/set-cookie', [CommonController::class, 'setCookie']);
Route::get('/reset-visual-options', [CommonController::class, 'resetVisualOptions']);
//download public page file
Route::get('/download/page/{file}', [CommonController::class, 'downloadPageFile'])->name('download.page.file');

//application
Route::controller(PdoiApplicationFrontController::class)->group(function () {
    Route::get('/application','index')->name('application.list');
    Route::get('/application/view/{id}','show')->name('application.show');
});

//statistics
Route::controller(\App\Http\Controllers\StatisticController::class)->group(function () {
    Route::get('/statistic','index')->name('statistic.list');
    Route::get('/statistic/view/{type}/{period?}','show')->name('statistic.view');
});

Route::group(['middleware' => ['auth', 'permission:'.implode('|',\App\Models\CustomRole::WEB_ACCESS_RULE)]], function() {

    Route::controller(CommonController::class)->group(function () {
        Route::get('/download/{file}', 'downloadFile')->name('download.file');
    });

    Route::controller(\App\Http\Controllers\UserController::class)->group(function () {
        Route::match(['get', 'put'], '/my-profile','profile')->name('profile');
    });

    //application
    Route::controller(PdoiApplicationFrontController::class)->group(function () {
        Route::get( '/my-application','myApplications')->name('application.my');
        Route::get( '/my-application/view/{id}','showMy')->name('application.my.show');
        Route::post( '/my-application/send-info','sendAdditionalInfo')->name('application.my.send_info');
        Route::get( '/my-application/full-history/{id}','showMyFullHistory')->name('application.my.show.history');
        Route::get('/application/new','create')->name('application.create');
        Route::post('/application/store','store')->name('application.store');
    });
});

//Admin
Route::controller(\App\Http\Controllers\Auth\Admin\LoginController::class)->group(function () {
    Route::get('/admin/login',                'showLoginForm')->name('login.admin');
    Route::post('/admin/login',         'login')->name('login.admin.submit');
});

require_once('admin.php');

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

//sitemap
Route::get('sitemap.xml', [\App\Http\Controllers\HomeController::class, 'sitemap'])->name('sitemap');
Route::get('sitemap/{subject_id?}', [\App\Http\Controllers\HomeController::class, 'subSitemap'])->name('sitemap.sub');

//help pages
Route::get('help-section', [\App\Http\Controllers\HomeController::class, 'help'])->name('help.index');
Route::get('help-section/{slug}', [\App\Http\Controllers\HomeController::class, 'helpPage'])->name('help.page');
Route::get('help-section/download/{file}', [\App\Http\Controllers\HomeController::class, 'downloadHelpDoc'])->name('help.download');

//Sections and pages
Route::get('{slug}', [\App\Http\Controllers\HomeController::class, 'section'])->name('section');
Route::get('{section_slug}/{slug}', [\App\Http\Controllers\HomeController::class, 'page'])->name('page');

Route::fallback(function(){
    Log::channel('info')->info('Path not found; User ip: '.request()->ip().'; Url: '.request()->getPathInfo());
    return response()->view('errors.404', [], 404);
});
