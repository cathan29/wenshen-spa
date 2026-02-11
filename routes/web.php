<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QueueController;

/*
|--------------------------------------------------------------------------
| 🏠 HOMEPAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| 🔓 PUBLIC ROUTES (No Login Required)
|--------------------------------------------------------------------------
| These MUST be outside the 'auth' middleware so clients/TVs can access them.
*/

// 1. Client Status Page (The link inside the QR Code)
Route::get('/queue/{qr_token}', [QueueController::class, 'show'])->name('queue.show');

// 2. Client Cancel Request (When client clicks "Cancel" on their phone)
Route::patch('/queue/request-cancel/{id}', [QueueController::class, 'requestCancel'])->name('queue.requestCancel');

// 3. TV Monitor Screen (So the Smart TV doesn't need to log in)
Route::get('/monitor', [QueueController::class, 'monitor'])->name('queue.monitor');


/*
|--------------------------------------------------------------------------
| 🔒 PROTECTED ROUTES (Receptionist/Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Dashboard Console
    Route::get('/dashboard', [QueueController::class, 'index'])->name('dashboard');
    
    // Queue Actions
    Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');
    Route::get('/queue/{id}/print', [QueueController::class, 'printTicket'])->name('queue.print');
    Route::patch('/queue/{id}/status', [QueueController::class, 'updateStatus'])->name('queue.updateStatus');
    Route::post('/queue/{id}/add-service', [QueueController::class, 'addService'])->name('queue.addService');

    // Service Management
    Route::resource('services', ServiceController::class);

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';