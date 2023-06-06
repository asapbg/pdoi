<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/locale', function (Request $request) {
    if ($request->has('locale')) {
        session(['locale' => $request->offsetGet('locale')]);
        app()->setLocale($request->offsetGet('locale'));
    }
    return back();
})->name('change-locale');


//Admin
Route::controller(\App\Http\Controllers\Auth\Admin\LoginController::class)->group(function () {
    Route::get('/admin/login',                'showLoginForm')->name('login.admin');
    Route::post('/admin/login',         'login')->name('login.admin.submit');
});

require_once('admin.php');

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});
