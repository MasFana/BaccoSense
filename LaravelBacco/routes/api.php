<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;


Route::post('/add', [App\Http\Controllers\AdminController::class, 'add_history'])
    ->name('add_history');
