<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Login;

use App\Http\Controllers\ProdukController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventarisController;

// Show the login form
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Handle the login request
Route::post('/login', [LoginController::class, 'login']);

// Log the user out
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/', function () {
    // return view('welcome');
    return redirect('login');
});


Route::group(['middleware' => ['auth']], function () {
    // Protected routes
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'produk'], function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk');
        Route::get('/edit/{id}', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::get('/tambah', [ProdukController::class, 'tambah'])->name('produk.tambah');
        Route::post('/', [ProdukController::class, 'store'])->name('produk.create');
        Route::post('/edit/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    });
    
    Route::group(['prefix' => 'inventaris'], function () {
        Route::get('/', [InventarisController::class, 'index'])->name('inventaris');
        Route::get('edit/{id}', [InventarisController::class, 'edit'])->name('inventaris.edit');
        Route::get('/arima', [InventarisController::class, 'arima'])->name('inventaris.arima');
    });

    
    Route::get('/penjualan', [InventarisController::class, 'index'])->name('penjualan');
    Route::get('/pembelian', [InventarisController::class, 'index'])->name('pembelian');
});
