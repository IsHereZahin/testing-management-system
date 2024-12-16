<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestCaseController;

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


// Route for viewing projects, accessible to testers and super-admins
Route::middleware(['auth', 'verified', 'role:tester|super-admin'])->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        Route::get('/projects', 'projects')->name('projects');
    });
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        Route::get('/project/add', 'create')->name('project.create');
        Route::post('/project/store', 'store')->name('project.store');
        Route::get('/project/edit/{id}', 'edit')->name('project.edit');
        Route::put('/project/update/{id}', 'update')->name('project.update');
        Route::delete('/project/delete/{id}', 'delete')->name('project.delete');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(PageController::class)->group(function () {
        Route::get('/project/{project}/pages', 'index')->name('page.index');
        Route::get('/project/{project}/page/create', 'create')->name('page.create');
        Route::post('/project/{project}/page/store', 'store')->name('page.store');
        Route::get('/project/{project}/page/{page}', 'show')->name('page.show');
    });

    Route::controller(TestCaseController::class)->group(function () {
        Route::get('/project/{project}/{page}/tests', 'index')->name('test.index');
        Route::get('/project/{project}/{page}/test/create', 'create')->name('test.create');
        Route::post('/project/{project}/{page}/test/store', 'store')->name('test.store');
        Route::get('project/{project}/{page}/test/{testCase}', 'show')->name('test.show');
        Route::get('project/{project}/{page}/test/{testCase}/edit', 'edit')->name('test.edit');
        Route::put('project/{project}/{page}/test/{testCase}/update', 'update')->name('test.update');
        Route::delete('/project/{project}/{page}/test/{testCase}/delete', 'delete')->name('test.delete');
        Route::put('/projects/{project}/pages/{page}/test-case/{id}/update-status', 'updateStatus')->name('test.update-status');
        Route::post('/project/{project}/reset-all-test-cases', 'resetAllTestCases')->name('test.resetAll');
    });
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::controller(PageController::class)->group(function () {
        Route::get('/project/{project}/page/{page}/edit', 'edit')->name('page.edit');
        Route::put('/project/{project}/page/{page}/update', 'update')->name('page.update');
        Route::delete('/project/{project}/page/{page}/delete', 'destroy')->name('page.destroy');
    });
});



// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
