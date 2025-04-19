<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

// Show the login form
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Handle the login request
Route::post('/login', [LoginController::class, 'login']);

// Log the user out
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/', function () {
    return view('welcome');
});


Route::group(['middleware' => ['auth']], function () {
    // Protected routes
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/admin', function () {
        return view('admin');
    })->name('admin');
});
