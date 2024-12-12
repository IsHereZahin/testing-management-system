<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
*/

// Welcome route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard route for users with 'tester' or 'super-admin' roles
Route::get('/dashboard', function () {
    return view('/dashboard/dashboard');
})->middleware(['auth', 'verified', 'role:tester|super-admin'])->name('dashboard');

// Admin Dashboard route for super-admin only
Route::get('/dashboard/admin', function () {
    return view('/dashboard/admin-dashboard');
})->middleware(['auth', 'verified', 'role:super-admin'])->name('admin_dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
