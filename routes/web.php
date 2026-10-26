<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Alur: Request -> Route -> Controller -> Model -> Database -> Response
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

// ---- Tamu (belum login) ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

// ---- Wajib login (admin & staff) ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Staff hanya boleh MELIHAT produk.
    Route::resource('products', ProductController::class)->only(['index']);

    // Barang masuk/keluar & histori: admin dan staff.
    Route::resource('stock', StockController::class)->only(['index', 'store']);

    // Laporan: admin dan staff.
    Route::resource('reports', ReportController::class)->only(['index']);

    // ---- Khusus Admin ----
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
});
