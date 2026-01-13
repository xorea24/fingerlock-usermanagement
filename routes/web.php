<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Auth::routes();

Route::controller(AdminController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/positions', 'positions')->name('positions');
});