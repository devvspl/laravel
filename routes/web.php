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
use App\Http\Controllers\admin\FilterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\admin\ClaimViewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Public Routes
// These routes are accessible without authentication
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Cache Management Routes
// Routes for clearing various types of application cache
Route::get('clear-all-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    return response()->json([
        'message' => 'All caches cleared successfully.'
    ]);
});

// Authentication Routes
// Default Laravel authentication routes
Auth::routes();

// Home Route
// Primary landing page after login
Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Public Dashboard Route
// Accessible without authentication
Route::get('overview', [DashboardController::class, 'overview'])->name('overview');

// Authenticated Routes
// All routes below require user authentication
Route::middleware('auth')->group(function () {

    // Dashboard Routes
    // Routes for dashboard-related functionality
    Route::get('dashboard', [DashboardController::class, 'overview'])->name('dashboard');
    Route::post('admin/overview/filter', [DashboardController::class, 'filter'])->name('admin.overview.filter');

    // Permission Management Routes
    // Routes for managing permissions and permission groups
    Route::resource('permissions', PermissionController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
    Route::post('permissions/assign', [PermissionController::class, 'assignPermissions'])->name('permissions.assign');
    Route::get('permissions-list', [PermissionController::class, 'getAllPermissions'])->name('permissions.all');

    // Role Management Routes
    // Routes for managing user roles
    Route::resource('roles', RolesController::class);
    Route::get('get-roles', [RolesController::class, 'getRoles'])->name('roles.getRoles');

    // User Management Routes
    // Routes for user management and profile operations
    Route::resource('users', UsersController::class);
    Route::get('users/{id}/permissions', [PermissionController::class, 'getPermissions'])->name('users.permissions');
    Route::post('users/{id}/permissions/assign', [PermissionController::class, 'assignPermission'])->name('users.permissions.assign');
    Route::post('users/{id}/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('users.permissions.revoke');
    Route::get('profile', [UsersController::class, 'profile'])->name('users.profile');

    // Menu Management Routes
    // Routes for managing system menus
    Route::resource('menu', MenuController::class);
    Route::get('menu-list', [MenuController::class, 'menuList'])->name('menu.all');

    // Financial Year Routes
    // Routes for managing financial year data
    Route::resource('financial', FinancialYearController::class);

    // Settings Routes
    // Routes for system and company configuration
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::get('company', [SettingController::class, 'company'])->name('company');
    Route::get('get-company-config/{id}', [SettingController::class, 'getCompanyConfig'])->name('company.config.get');
    Route::post('save-config', [SettingController::class, 'saveCompanyConfig'])->name('company.config.save');

    // Report Routes
    // Routes for generating and exporting reports
    Route::get('claim-report', [ReportController::class, 'claimReport'])->name('admin.claim_report');
    Route::get('daily-activity', [ReportController::class, 'dailyActivity'])->name('daily-activity');
    Route::get('/functions', [ReportController::class, 'getFunction'])->name('report.functions');
    Route::get('/verticals', [ReportController::class, 'getVertical'])->name('report.verticals');
    Route::get('/departments', [ReportController::class, 'getDepartment'])->name('report.departments');
    Route::get('/claim-types', [ReportController::class, 'getClaimTypes'])->name('report.claim_types');
    Route::post('/filter-claims', [ReportController::class, 'filterClaims'])->name('report.filter_claims');
    Route::post('/expense-claims/export', [ReportController::class, 'export'])->name('expense-claims.export');
    Route::post('/daily-activity/data', [ReportController::class, 'getDailyActivityData'])->name('daily-activity.data');
    Route::post('/daily-activity/export', [ReportController::class, 'exportDailyActivity'])->name('daily-activity.export');

    // Filter Routes
    // Routes for filter select options
    Route::post('employees/by-department', [FilterController::class, 'getEmployeesByDepartment']);
    Route::post('verticals/by-function', [FilterController::class, 'getVerticalsByFunction']);
    Route::post('departments/by-vertical', [FilterController::class, 'getDepartmentsByVertical']);
    Route::post('sub-departments/by-department', [FilterController::class, 'getSubDepartmentsByDepartment']);
    Route::post('employees/by-department', [FilterController::class, 'getEmployeesByDepartment']);

    // Claim Routes
    Route::get('/get-claim-detail-view', [ClaimViewController::class, 'getClaimDetailView']);

    // Supoort Message
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/messages', [ChatController::class, 'fetchMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

});