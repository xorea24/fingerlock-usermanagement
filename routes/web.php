<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;



/*
|--------------------------------------------------------------------------
| HARDWARE & API ENDPOINTS (Fingerlock Communication)
|--------------------------------------------------------------------------
*/

// Heartbeat route for the Fingerlock hardware to report status
Route::post('/hardware/ping', [SettingsController::class, 'heartbeat'])->name('hardware.ping');

// Called by hardware on every fingerprint scan — logs success/failed attempt
Route::post('/hardware/access', [SettingsController::class, 'reportAccess'])->name('hardware.access');

// Endpoint for the hardware to fetch the latest access configurations/rules
Route::get('/settings/latest', [SettingsController::class, 'getLatestData']);


/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User Management (Enrolling/Deleting Fingerprint Users)
    Route::resource('users', UserController::class)->except(['show']);
    Route::delete('/User/{id}', [UserController::class, 'destroy']);

    // Audit Logs (Monitoring who accessed the lock and when)
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/log-audit', [AuditController::class, 'index'])->name('logs.index');

    // Lock Settings (Duration, Security Levels, etc.)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'indexPage'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::patch('/update', [SettingsController::class, 'update'])->name('patch');
    });

});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION & REDIRECTS
|--------------------------------------------------------------------------
*/

Auth::routes(['register' => false]); // Usually, you don't want public registration for a lock system

Route::get('/', function () {
    return redirect('/login');
});



Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
});

Route::post('/logout', [UserController::class, 'logout'])->name('logout');