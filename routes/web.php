<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\PdoiApplicationController as PdoiApplicationFrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('front.logout');


Route::get('/locale', function (Request $request) {
    if ($request->has('locale')) {
        session(['locale' => $request->offsetGet('locale')]);
        app()->setLocale($request->offsetGet('locale'));
    }
    return back();
})->name('change-locale');

//pdoi subjects modal
Route::get('/get-pdoi-subjects', [CommonController::class, 'modalPdoiSubjects'])->name('modal.pdoi_subjects');

//application
Route::controller(PdoiApplicationFrontController::class)->group(function () {
    Route::get('/application','index')->name('application.list');
    Route::get('/application/view/{id}','show')->name('application.show');
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

//Sections and pages
Route::get('{slug}', [\App\Http\Controllers\HomeController::class, 'section'])->name('section');
Route::get('{section_slug}/{slug}', [\App\Http\Controllers\HomeController::class, 'page'])->name('page');

Route::fallback(function(){
    Log::channel('info')->info('Path not found; User ip: '.request()->ip().'; Url: '.request()->getPathInfo());
    return response()->view('errors.404', [], 404);
});
