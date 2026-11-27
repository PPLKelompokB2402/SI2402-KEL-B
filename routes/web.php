<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VendorPortalController;
use Illuminate\Support\Facades\Route;

/*
| Pelanggan (publik) — PBI-04, 05, 06, 14
*/
Route::get('/', [CatalogController::class, 'index'])->name('vendors.index');
Route::get('/vendors/{vendor}', [CatalogController::class, 'show'])->name('vendors.show');
Route::get('/vendors/{vendor}/portofolio', [CatalogController::class, 'portofolio'])->name('vendors.portofolio');
Route::get('/vendors/{vendor}/harga', [CatalogController::class, 'harga'])->name('vendors.harga');

Route::get('/media/portofolio/{portofolio}', [MediaController::class, 'portofolio'])->name('media.portofolio');
Route::get('/media/dokumen/{dokumen}', [MediaController::class, 'dokumen'])->name('media.dokumen');

/*
| Autentikasi — login admin & super admin memakai URL terpisah (PBI-10)
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::get('/admin/login', [AuthController::class, 'adminLoginForm'])->middleware('guest:admin')->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware(['guest:admin', 'throttle:10,1'])->name('admin.login.store');
Route::get('/super-admin/login', [AuthController::class, 'superAdminLoginForm'])->middleware('guest:super_admin')->name('super-admin.login');
Route::post('/super-admin/login', [AuthController::class, 'superAdminLogin'])->middleware(['guest:super_admin', 'throttle:10,1'])->name('super-admin.login.store');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->middleware('auth:admin')->name('admin.logout');
Route::post('/super-admin/logout', [AuthController::class, 'superAdminLogout'])->middleware('auth:super_admin')->name('super-admin.logout');


/*
| Portal vendor — PBI-01, 02, 03, 13
*/
Route::prefix('vendor')->name('vendor.')->middleware(['auth:web', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/usaha', [VendorPortalController::class, 'usaha'])->name('usaha');
    Route::post('/usaha', [VendorPortalController::class, 'simpanUsaha'])->name('usaha.simpan');
    Route::put('/usaha/{vendor}', [VendorPortalController::class, 'updateUsaha'])->name('usaha.update');
    Route::get('/profil', [VendorPortalController::class, 'profil'])->name('profil');
    Route::post('/usaha/{vendor}/legalitas', [VendorPortalController::class, 'unggahLegalitas'])->name('legalitas.unggah');
    Route::get('/portofolio', [VendorPortalController::class, 'portofolio'])->name('portofolio');
    Route::post('/usaha/{vendor}/portofolio', [VendorPortalController::class, 'simpanPortofolio'])->name('portofolio.simpan');
    Route::delete('/portofolio/{portofolio}', [VendorPortalController::class, 'hapusPortofolio'])->name('portofolio.hapus');
});

/*
| Portal administrator — PBI-07, 08, 09, 16, 17, 18, 19
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi', [AdminController::class, 'verifikasi'])->name('verifikasi');
    Route::get('/verifikasi/{vendor}', [AdminController::class, 'detailVerifikasi'])->name('verifikasi.detail');
    Route::patch('/dokumen/{dokumen}', [AdminController::class, 'periksaDokumen'])->name('dokumen.periksa');
    Route::patch('/vendors/{vendor}/status', [AdminController::class, 'ubahStatusVendor'])->name('vendors.status');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
});

/*
| Portal super admin — US-11 kelola akun administrator (+ kategori)
*/
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth:super_admin', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    // US-11 — kelola akun administrator
    Route::post('/administrator', [AdministratorController::class, 'store'])->name('administrator.store');
    Route::put('/administrator/{user}', [AdministratorController::class, 'update'])->name('administrator.update');
    Route::patch('/administrator/{user}/status', [AdministratorController::class, 'ubahStatus'])->name('administrator.status');
    Route::delete('/administrator/{user}', [AdministratorController::class, 'destroy'])->name('administrator.destroy');
});
