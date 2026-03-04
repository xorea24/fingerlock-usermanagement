

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Added missing Auth import
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SettingsController;

Auth::routes();

//Settings Section
Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');

// PhotoController::class, 'restoreAlbum' Section 
Route::get('/recycle', [AlbumController::class, 'recycle'])->name('recycle.index');
Route::patch('/{id}/restore', [PhotoController::class, 'restore'])->name('photos.restore');
Route::delete('/{albumId}/force-delete', [AlbumController::class, 'forceDeleteAlbum'])->name('Photo.delete-album');
Route::delete('/photos/{id}/force', [PhotoController::class, 'forceDelete'])->name('photos.forceDelete');

// Add these to fix the "Route not found" errors in your Blade files
Route::patch('/photos/restore-album', [AlbumController::class, 'restoreAlbum'])->name('photos.restore-album');
Route::delete('/photos/force-delete-album/{id}', [AlbumController::class, 'forceDeleteAlbum'])->name('Photo.delete-album');
Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');

// Public Routes
Route::get('/', [PhotoController::class, 'publicGallery'])->name('home');
Route::get('/gallery', [PhotoController::class, 'publicGallery'])->name('gallery.public');
Route::get('/albums', [AlbumController::class, 'albums'])->name('albums');

// Photos Group
Route::middleware(['auth'])->group(function () {
    // PHOTO MANAGEMENT
    Route::post('/upload', [PhotoController::class, 'store'])->name('photos.store');
    // Siguraduhin na 'photos.update' ang name at PATCH ang method
    // Siguraduhin na PATCH ito at tumutugma ang 'photo.update'
    Route::patch('{photo}', [PhotoController::class, 'update'])->name('photos.update');
    Route::get('/photos/{photo}/toggle', [PhotoController::class, 'toggle'])->name('photos.toggle');
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
        // 4. DESTROY / DELETE
    });

// Albums Group
Route::controller(AlbumController::class)
    ->prefix('albums')
    ->name('albums.') 
    ->group(function () {
        // Ensure this points to the correct Controller and Method
        Route::get('/recycle', [AlbumController::class, 'recycle'])->name('recycle.index');
        Route::get('/', 'index')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::post('/', 'store')->name('store');  
        Route::get('/{id}', 'show')->name('show'); 
        Route::patch('/{id}', 'update')->name('update');
        
        // FIXED: Removed the extra 'albums.' prefix because it is already in the group name
        Route::delete('/{albumId}/force-delete', [AlbumController::class, 'forceDeleteAlbum'])->name('Photo.delete-album');
    });



// Applicants Group
Route::controller(PhotoController::class)
    ->prefix('recycle')
    ->name('recycle.')
    ->group(function () {
        Route::get('recycle', 'index')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/search', 'search')->name('search');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });

// Interviews Group
Route::controller(SettingsController::class)
    ->prefix('settings')
    ->name('settings')
    ->group(function () {
        Route::get('/', 'indexPage')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/search', 'search')->name('search');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });