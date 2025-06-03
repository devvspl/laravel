<?php
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\PermissionGroupController;
use App\Http\Controllers\admin\RolesController;
use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\FinancialYearController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware('auth')->group(function () {
    Route::resource('permissions', PermissionController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('users', UsersController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('financial', FinancialYearController::class);
    Route::get('dashboard', [DashboardController::class, 'overview'])->name('dashboard');
    Route::post('permissions/assign', [PermissionController::class, 'assignPermissions'])->name('permissions.assign');
    Route::get('get-roles', [RolesController::class, 'getRoles'])->name('roles.getRoles');
    Route::post('admin/overview/filter', [DashboardController::class, 'filter'])->name('admin.overview.filter');
    Route::get('users/{id}/permissions', [PermissionController::class, 'getPermissions'])->name('users.permissions');
    Route::get('permissions-list', [PermissionController::class, 'getAllPermissions'])->name('permissions.all');
    Route::post('users/{id}/permissions/assign', [PermissionController::class, 'assignPermission'])->name('users.permissions.assign');
    Route::post('users/{id}/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('users.permissions.revoke');
    Route::get('profile', [UsersController::class, 'profile'])->name('users.profile');
    Route::get('menu-list', [MenuController::class, 'menuList'])->name('menu.all');
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::get('company', [SettingController::class, 'company'])->name('company');
    Route::get('get-company-config/{id}', [SettingController::class, 'getCompanyConfig']);
    Route::post('save-config', [SettingController::class, 'saveCompanyConfig']);
    Route::get('claim-report', [ReportController::class, 'claimReport'])->name('claim-report');

});
Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');