<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});


Route::controller(\App\Http\Controllers\Auth\Admin\LoginController::class)->group(function () {
    Route::get('/admin/login',                'showLoginForm')->name('login.admin');
    Route::post('/admin/login',         'login')->name('login.admin.submit');
});

require_once('admin.php');
