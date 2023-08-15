<?php
//eAuthentication routes
use Illuminate\Support\Facades\Route;

Route::get('/metadata/info/saml', [\App\Http\Controllers\EAuthController::class, 'spMetadata'])->name('eauth.sp_metadata');
Route::match(['get'],'/eauth/login', [\App\Http\Controllers\EAuthController::class, 'login'])->name('eauth.login')->middleware('guest');
Route::match(['post'],'/eauth/login-callback', [\App\Http\Controllers\EAuthController::class, 'loginCallback'])->name('eauth.login.callback');
Route::match(['post'],'/eauth/create-user', [\App\Http\Controllers\EAuthController::class, 'createUserSubmit'])->name('eauth.user.create');
Route::get('/eauth/logout', [\App\Http\Controllers\EAuthController::class, 'logout'])->name('eauth.logout');
