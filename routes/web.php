<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QueueController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // 1. Show the Dashboard (Receptionist Console)
    Route::get('/dashboard', [QueueController::class, 'index'])->name('dashboard');
    
    // 2. The "Add to Queue" Action
    Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');
    
    // 3. Admin Tools
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('services', ServiceController::class);
});

// 👇 THIS IS THE FIX: The Public Route for Clients 👇
// It is OUTSIDE the "middleware('auth')" group, so anyone can see it.
Route::get('/q/{qr_token}', [QueueController::class, 'show'])->name('queue.show');

require __DIR__.'/auth.php';