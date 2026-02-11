<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PositionController;

Auth::routes();

Route::controller(AdminController::class)->group(function () {
    Route::get('/', 'index')->name('home');
});

Route::controller(PositionController::class)
    ->prefix('positions')
    ->group(function () {
        Route::get('/', 'indexPage')->name('positions.index');
        Route::get('/datatable', 'datatable')->name('positions.datatable');
        Route::get('/search', 'search')->name('positions.search');
        Route::get('/{id}', 'show')->name('positions.show');
        Route::post('/', 'store')->name('positions.store');
        Route::put('/{id}', 'update')->name('positions.update');
        Route::delete('/{id}', 'delete')->name('positions.delete');
    });

Route::controller(ApplicantController::class)
    ->prefix('applicants')
    ->group(function () {
        Route::get('/', 'indexPage')->name('applicants.index');
        Route::get('/datatable', 'datatable')->name('applicants.datatable');
        Route::get('/search', 'search')->name('applicants.search');
        Route::get('/{id}', 'show')->name('applicants.show');
        Route::post('/', 'store')->name('applicants.store');
        Route::put('/{id}', 'update')->name('applicants.update');
        Route::delete('/{id}', 'delete')->name('applicants.delete');
    });

Route::controller(InterviewController::class)
    ->prefix('interviews')
    ->group(function () {
        Route::get('/', 'indexPage')->name('interviews.index');
        Route::get('/datatable', 'datatable')->name('interviews.datatable');
        Route::get('/search', 'search')->name('interviews.search');
        Route::get('/{id}', 'show')->name('interviews.show');
        Route::post('/', 'store')->name('interviews.store');
        Route::put('/{id}', 'update')->name('interviews.update');
        Route::delete('/{id}', 'delete')->name('interviews.delete');
    });