<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VendorPortalController;
use Illuminate\Support\Facades\Route;

/*
| Beranda — katalog vendor menyusul di PBI-04
*/
Route::view('/', 'home')->name('vendors.index');


/*
| Autentikasi — login & register dengan session (satu halaman login untuk semua peran)
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');


/*
| Portal vendor — PBI-01, 02, 03, 13
*/
Route::prefix('vendor')->name('vendor.')->middleware(['auth:web', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/usaha', [VendorPortalController::class, 'usaha'])->name('usaha');
    Route::post('/usaha', [VendorPortalController::class, 'simpanUsaha'])->name('usaha.simpan');
    Route::put('/usaha/{vendor}', [VendorPortalController::class, 'updateUsaha'])->name('usaha.update');
    Route::get('/profil', [VendorPortalController::class, 'profil'])->name('profil');
});

/*
| Portal administrator — PBI-07, 08, 09, 16, 17, 18, 19
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:web', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
});

/*
| Portal super admin — PBI-11, 12 (+ kategori & audit log)
*/
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth:web', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});
