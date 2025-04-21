<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Auth\Events\Login;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\LoginController;

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

    Route::get('/admin', function () {
        return view('admin');
    })->name('admin');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
    Route::get('/produk/edit/{id}', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::get('/produk/tambah', [ProdukController::class, 'tambah'])->name('produk.tambah');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.create');
    Route::post('/produk/edit/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    
    // Route::post('/produk/store', [ProdukController::class, 'store'])->name('produkStore');
    // Route::post('/produk/update/{id}', [ProdukController::class, 'update'])->name('produkUpdate');
});
