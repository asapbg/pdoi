<?php
//eAuthentication routes
use Illuminate\Support\Facades\Route;

Route::get('/metadata/info/saml/{callback_source?}', [\App\Http\Controllers\EAuthController::class, 'spMetadata'])->name('eauth.sp_metadata');
Route::get('/eauth/login/{source?}', [\App\Http\Controllers\EAuthController::class, 'login'])->name('eauth.login')->middleware('guest');
Route::post('/eauth/login-callback/{source?}', [\App\Http\Controllers\EAuthController::class, 'loginCallback'])->name('eauth.login.callback');
Route::post('/eauth/create-user', [\App\Http\Controllers\EAuthController::class, 'createUserSubmit'])->name('eauth.user.create');
Route::get('/eauth/logout', [\App\Http\Controllers\EAuthController::class, 'logout'])->name('eauth.logout');
