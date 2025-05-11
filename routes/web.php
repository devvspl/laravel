<?php
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\PermissionGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin routes
Route::middleware('auth')->group(function () {
    Route::resource('permissions', PermissionController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
});


Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');