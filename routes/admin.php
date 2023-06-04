<?php

use App\Http\Controllers\{Auth\Admin\LoginController as AdminLoginControllerAlias, CommonController};
// Admin
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\{PdoiResponseSubjectController as PdoiResponseSubjectControllerAlias,
    HomeController as AdminHomeController,
    RzsSectionController,
    ActivityLogController,
    PermissionsController,
    UsersController,
    RolesController};

use App\Http\Controllers\Admin\Nomenclature\EkatteAreaController;
use App\Http\Controllers\Admin\Nomenclature\EkatteMunicipalityController;
use App\Http\Controllers\Admin\Nomenclature\EkatteSettlementController;
use App\Http\Controllers\Admin\Nomenclature\CountryController;
use App\Http\Controllers\Admin\Nomenclature\ProfileTypeController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::match(['get'],'/admin/logout', [AdminLoginControllerAlias::class, 'logout'])->name('admin.logout');
// Common routes
Route::group(['middleware' => ['auth', 'administration']], function() {

    Route::get('/admin', [AdminHomeController::class, 'index'])->name('admin.home');
//    Route::get('/admin', [AdminHomeController::class, 'index'])->name('admin.home');
//    Route::match(['get'],'/logout', [LoginController::class, 'logout'])->name('logout');
//    Route::match(['get', 'post'],'/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/locale', function (Request $request) {
        if ($request->has('locale')) {
            session(['locale' => $request->offsetGet('locale')]);
            app()->setLocale($request->offsetGet('locale'));
        }
        return back();
    })->name('change-locale');

    Route::controller(CommonController::class)->group(function () {
        Route::get('/toggle-boolean', 'toggleBoolean')->name('toggle-boolean');
        Route::get('/toggle-permissions', 'togglePermissions')->name('toggle-permissions');
    });

    Route::fallback(function(){
        Log::channel('info')->info('Path not found; User ip: '.request()->ip().'; Url: '.request()->getPathInfo());
        return response()->view('errors.404', [], 404);
    });
});

// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'administration', 'validated']], function() {

    Route::controller(UsersController::class)->group(function () {
        Route::name('users.profile.edit')->get('/users/profile/{user}/edit', 'editProfile');
        Route::name('users.profile.update')->post('/users/profile/{user}/update', 'updateProfile');
    });

    Route::controller(UsersController::class)->group(function () {
        Route::get('/users',                'index')->name('users');
        Route::get('/users/create',         'create')->name('users.create');
        Route::post('/users/store',         'store')->name('users.store');
        Route::get('/users/{user}/edit',    'edit')->name('users.edit');
        Route::match(['post', 'put'], '/users/{user}/update', 'update')->name('users.update');
        Route::get('/users/{user}/delete',  'destroy')->name('users.delete');
        Route::get('/users/export',         'export')->name('users.export');
    });

    Route::controller(RolesController::class)->group(function () {
        Route::get('/roles',                'index')->name('roles');
        Route::get('/roles/create',         'create')->name('roles.create');
        Route::post('/roles/store',         'store')->name('roles.store');
        Route::get('/roles/{role}/edit',    'edit')->name('roles.edit');
        Route::post('/roles/{role}/update',  'update')->name('roles.update');
        Route::post('/roles/{role}/delete',  'destroy')->name('roles.delete');
        Route::post('/roles/{role}/add-users',  'addUsers')->name('roles.users.add');
        Route::post('/roles/{role}/remove-users',  'removeUsers')->name('roles.users.remove');
    });

    Route::controller(PermissionsController::class)->group(function () {
        Route::get('/permissions',                      'index')->name('permissions');
        Route::get('/permissions/create',               'create')->name('permissions.create');
        Route::post('/permissions/store',               'store')->name('permissions.store');
        Route::get('/permissions/{permission}/edit',    'edit')->name('permissions.edit');
        Route::get('/permissions/{permission}/update',  'update')->name('permissions.update');
        Route::get('/permissions/{permission}/delete',  'destroy')->name('permissions.delete');
        Route::post('/permissions/roles',               'rolesPermissions')->name('permissions.roles');
    });

    Route::controller(ActivityLogController::class)->group(function () {
        Route::get('/activity-logs',                 'index')->name('activity-logs');
        Route::get('/activity-logs/{activity}/show', 'show')->name('activity-logs.show');
    });

    Route::controller(RzsSectionController::class)->group(function () {
        Route::get('/rzs-sections',                'index')->name('rzs.sections')->middleware('can:viewAny,App\Models\PdoiResponseSubject');
        Route::get('/rzs-sections/edit/{item?}',         'edit')->name('rzs.sections.edit');
        Route::match(['post', 'put'], '/rzs-sections/store/{item?}',         'store')->name('rzs.sections.store');
    });

    Route::controller(PdoiResponseSubjectControllerAlias::class)->group(function () {
        Route::get('/rzs-subjects',                'index')->name('rzs')->middleware('can:viewAny,App\Models\PdoiResponseSubject');
        Route::get('/rzs-subjects/edit/{item?}',         'edit')->name('rzs.edit');
        Route::match(['post', 'put'], '/rzs-subjects/store/{item?}',         'store')->name('rzs.store');
        Route::get('/rzs-subjects/{subject}/delete',  'delete')->name('rzs.delete');
    });

    //Nomenclature
    Route::controller(CountryController::class)->group(function () {
        Route::get('/nomenclature/country',                'index')->name('nomenclature.ekatte.country')->middleware('can:viewAny,App\Models\Country');
        Route::get('/nomenclature/country/edit/{item?}',         'edit')->name('nomenclature.ekatte.country.edit');
        Route::match(['post', 'put'], '/nomenclature/country/store/{item?}',         'store')->name('nomenclature.ekatte.country.store');
    });

    Route::controller(EkatteAreaController::class)->group(function () {
        Route::get('/nomenclature/area',                'index')->name('nomenclature.ekatte.area')->middleware('can:viewAny,App\Models\EkatteArea');
        Route::get('/nomenclature/area/edit/{item?}',         'edit')->name('nomenclature.ekatte.area.edit');
        Route::match(['post', 'put'], '/nomenclature/area/store/{item?}',         'store')->name('nomenclature.ekatte.area.store');
    });

    Route::controller(EkatteMunicipalityController::class)->group(function () {
        Route::get('/nomenclature/municipality',                'index')->name('nomenclature.ekatte.municipality')->middleware('can:viewAny,App\Models\EkatteMunicipality');
        Route::get('/nomenclature/municipality/edit/{item?}',         'edit')->name('nomenclature.ekatte.municipality.edit');
        Route::match(['post', 'put'], '/nomenclature/municipality/store/{item?}',         'store')->name('nomenclature.ekatte.municipality.store');
    });

    Route::controller(EkatteSettlementController::class)->group(function () {
        Route::get('/nomenclature/settlement',                'index')->name('nomenclature.ekatte.settlement')->middleware('can:viewAny,App\Models\EkatteSettlement');
        Route::get('/nomenclature/settlement/edit/{item?}',         'edit')->name('nomenclature.ekatte.settlement.edit');
        Route::match(['post', 'put'], '/nomenclature/settlement/store/{item?}',         'store')->name('nomenclature.ekatte.settlement.store');
    });

    Route::controller(ProfileTypeController::class)->group(function () {
        Route::get('/nomenclature/profile-type',                'index')->name('nomenclature.profile_type')->middleware('can:viewAny,App\Models\ProfileType');
        Route::get('/nomenclature/profile-type/edit/{item?}',         'edit')->name('nomenclature.profile_type.edit');
        Route::match(['post', 'put'], '/nomenclature/profile-type/store/{item?}',         'store')->name('nomenclature.profile_type.store');
    });

});
