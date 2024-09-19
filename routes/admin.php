<?php

use App\Models\NoConsiderReason;
use App\Http\Controllers\{Auth\Admin\LoginController as AdminLoginControllerAlias, CommonController};
// Admin
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\{PdoiResponseSubjectController as PdoiResponseSubjectControllerAlias,
    HomeController as AdminHomeController,
    RzsSectionController,
    ActivityLogController,
    PermissionsController,
    UsersController,
    RolesController, PdoiApplicationController as AdminPdoiApplicationController,
};

use App\Http\Controllers\Admin\Nomenclature\EkatteAreaController;
use App\Http\Controllers\Admin\Nomenclature\EkatteMunicipalityController;
use App\Http\Controllers\Admin\Nomenclature\EkatteSettlementController;
use App\Http\Controllers\Admin\Nomenclature\CountryController;
use App\Http\Controllers\Admin\Nomenclature\ProfileTypeController;
use App\Http\Controllers\Admin\Nomenclature\CategoryController;
use App\Http\Controllers\Admin\Nomenclature\ExtendTermsReasonController;
use App\Http\Controllers\Admin\Nomenclature\ReasonRefusalController;
use App\Http\Controllers\Admin\Nomenclature\EventController;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::match(['get'],'/admin/logout', [AdminLoginControllerAlias::class, 'logout'])->name('admin.logout');
// Common routes
Route::group(['middleware' => ['auth', 'administration']], function() {

    //Route::get('/admin', [AdminHomeController::class, 'index'])->name('admin.home');
    Route::get('/admin', [\App\Http\Controllers\Admin\PdoiApplicationController::class, 'index'])->name('admin.home');

    Route::controller(CommonController::class)->group(function () {
        Route::get('/toggle-boolean', 'toggleBoolean')->name('toggle-boolean');
        Route::get('/toggle-permissions', 'togglePermissions')->name('toggle-permissions');
    });
});

// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'administration']], function() {

    Route::get('/support-notifications', [\App\Http\Controllers\Admin\SupportController::class, 'notifications'])->name('support.notifications');
    Route::get('/support-notifications/{id}/view', [\App\Http\Controllers\Admin\SupportController::class, 'notificationView'])->name('support.notifications.view');
    Route::get('/admin', [AdminHomeController::class, 'guide'])->name('help.guide');
    Route::get('/help', [AdminHomeController::class, 'faq'])->name('help.faq');

    Route::controller(CommonController::class)->group(function () {
        Route::get('/download/{file}', 'downloadFile')->name('download.file');
        Route::get('/delete/{file}', 'deleteFile')->name('delete.file');
        Route::post('/upload-file/{object_id}/{object_type}','uploadFile')->name('upload.file');
    });

    Route::controller(UsersController::class)->group(function () {
        Route::name('users.profile.edit')->get('/users/profile/{user}/edit', 'editProfile');
        Route::name('users.profile.update')->post('/users/profile/{user}/update', 'updateProfile');
        Route::name('users.profile.notifications')->get('/my-notifications', 'myNotifications');
        Route::name('users.profile.notifications.show')->get('/my-notifications/{id}/show', 'showMyNotifications');
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
        Route::get('/rzs-subjects/view/{item?}',         'show')->name('rzs.view');
        Route::match(['post', 'put'], '/rzs-subjects/store/{item?}',         'store')->name('rzs.store');
        Route::get('/rzs-subjects/{subject}/delete',  'delete')->name('rzs.delete');
        Route::match(['get', 'post'],'/rzs-subjects/import',  'import')->name('rzs.import');
        Route::get('/rzs-subjects/import/download-example-file',  'downloadImportTemplateFile')->name('rzs.import.download_example');
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

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/nomenclature/category',                'index')->name('nomenclature.category')->middleware('can:viewAny,App\Models\Category');
        Route::get('/nomenclature/category/edit/{item?}',         'edit')->name('nomenclature.category.edit');
        Route::match(['post', 'put'], '/nomenclature/category/store/{item?}',         'store')->name('nomenclature.category.store');
    });

    Route::controller(ExtendTermsReasonController::class)->group(function () {
        Route::get('/nomenclature/extend-terms',                'index')->name('nomenclature.extend_terms')->middleware('can:viewAny,App\Models\ExtendTermsReason');
        Route::get('/nomenclature/extend-terms/edit/{item?}',         'edit')->name('nomenclature.extend_terms.edit');
        Route::match(['post', 'put'], '/nomenclature/extend-terms/store/{item?}',         'store')->name('nomenclature.extend_terms.store');
    });

    Route::controller(ReasonRefusalController::class)->group(function () {
        Route::get('/nomenclature/reason-refusal',                'index')->name('nomenclature.reason_refusal')->middleware('can:viewAny,App\Models\ReasonRefusal');
        Route::get('/nomenclature/reason-refusal/edit/{item?}',         'edit')->name('nomenclature.reason_refusal.edit');
        Route::match(['post', 'put'], '/nomenclature/reason-refusal/store/{item?}',         'store')->name('nomenclature.reason_refusal.store');
    });

    Route::controller(\App\Http\Controllers\Admin\Nomenclature\NoConsiderationReasonController::class)->group(function () {
        Route::get('/nomenclature/no-consider-reason',                'index')->name('nomenclature.no_consider_reason')->middleware('can:viewAny,App\Models\NoConsiderReason');
        Route::get('/nomenclature/no-consider-reason/edit/{item?}',         'edit')->name('nomenclature.no_consider_reason.edit');
        Route::match(['post', 'put'], '/nomenclature/no-consider-reason/store/{item?}',         'store')->name('nomenclature.no_consider_reason.store');
    });

    Route::controller(\App\Http\Controllers\Admin\Nomenclature\ChangeDecisionReasonController::class)->group(function () {
        Route::get('/nomenclature/change-decision-reason',                'index')->name('nomenclature.change_decision_reason')->middleware('can:viewAny,App\Models\ChangeDecisionReason');
        Route::get('/nomenclature/change-decision-reason/edit/{item?}',         'edit')->name('nomenclature.change_decision_reason.edit');
        Route::match(['post', 'put'], '/nomenclature/change-decision-reason/store/{item?}',         'store')->name('nomenclature.change_decision_reason.store');
    });

    Route::controller(EventController::class)->group(function () {
        Route::get('/nomenclature/event',                'index')->name('nomenclature.event')->middleware('can:viewAny,App\Models\Event');
        Route::get('/nomenclature/event/edit/{item?}',         'edit')->name('nomenclature.event.edit');
        Route::match(['post', 'put'], '/nomenclature/event/store/{item?}',         'store')->name('nomenclature.event.store');
    });

    //Applications
    Route::controller(AdminPdoiApplicationController::class)->group(function () {
        Route::get('/applications',                'index')->name('application')->middleware('can:viewAny,App\Models\PdoiApplication');
        Route::match(['get', 'post'],'/applications/create',         'create')->name('application.create');
        Route::get('/applications/view/{item?}',         'show')->name('application.view');
        Route::get('/applications/renew/{item?}',         'renew')->name('application.renew');
        Route::get('/applications/full-history/{item?}',         'showFullHistory')->name('application.history');
        Route::post('/applications/renew',         'renewSubmit')->name('application.renew.submit');
        Route::post('/applications/add-category',         'addCategory')->name('application.category.add');
        Route::post('/applications/remove-category',         'removeCategory')->name('application.category.remove');
        Route::get('/applications/{item}/new-event/{event}',         'newEvent')->name('application.event.new');
        Route::post('/applications/new-event/store',         'storeNewEvent')->name('application.event.new.store');
    });

    Route::controller(\App\Http\Controllers\Admin\MenuSectionController::class)->group(function () {
        Route::get('/menu-section/section',                'index')->name('menu_section')->middleware('can:viewAny,App\Models\MenuSection');
        Route::get('/menu-section/section/edit/{item?}',         'edit')->name('menu_section.edit');
        Route::match(['post', 'put'], '/menu-section/section/store/{item?}',         'store')->name('menu_section.store');
    });

    Route::controller(\App\Http\Controllers\Admin\PageController::class)->group(function () {
        Route::get('/menu-section/page',                'index')->name('page')->middleware('can:viewAny,App\Models\Page');
        Route::get('/menu-section/page/edit/{item?}',         'edit')->name('page.edit');
        Route::match(['post', 'put'], '/menu-section/page/store/{item?}',         'store')->name('page.store');
    });

    Route::controller(\App\Http\Controllers\Admin\MailTemplatesController::class)->group(function () {
        Route::get('/mail-template',                'index')->name('mail_template')->middleware('can:viewAny,App\Models\MailTemplates');
        Route::get('/mail-template/edit/{item?}',         'edit')->name('mail_template.edit');
        Route::match(['put'], '/mail-template/store/{item?}',         'store')->name('mail_template.store');
    });

    Route::controller(\App\Http\Controllers\Admin\SettingsController::class)->group(function () {
        Route::get('/settings/{section?}',                'index')->name('settings')->middleware('can:viewAny,App\Models\Settings');
        Route::match(['put'], '/settings',         'store')->name('settings.store');
    });

    Route::controller(\App\Http\Controllers\Admin\Statistic::class)->group(function () {
        Route::get('/statistic',                'index')->name('statistic');
        Route::get('/statistic/{type}',                'statistic')->name('statistic.type');
    });

    Route::controller(\App\Http\Controllers\Admin\PdoiApplicationRestoreRequestController::class)->group(function () {
        Route::get('/restore-requests',                'index')->name('restore_requests')->middleware('can:viewAny,App\Models\PdoiApplicationRestoreRequest');
        Route::get('/restore-requests/edit/{item?}',         'edit')->name('restore_requests.edit');
        Route::get('/restore-requests/view/{item?}',         'show')->name('restore_requests.view');
        Route::put('/restore-requests/reject',         'reject')->name('restore_requests.reject');
    });

    Route::controller(\App\Http\Controllers\Admin\NotificationsController::class)->group(function () {
        Route::get('/notifications',                'index')->name('notifications')->middleware('can:manage.*');
        Route::get('/notifications/create',         'create')->name('notifications.create');
        Route::post('/notifications/store',         'store')->name('notifications.store');
        Route::get('/notifications/view/{id?}',         'show')->name('notifications.view');
    });

});
