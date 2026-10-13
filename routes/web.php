<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SiteProjectController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Auth::routes();

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard (all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Admin Guide
    Route::get('/admin-guide', function () {
        return view('admin-guide');
    })->name('admin.guide');

    // Tasks (all authenticated users can view)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/kanban', [TaskController::class, 'kanban'])->name('tasks.kanban');

    // Implementation team - submit requests
    Route::middleware(['role:Implementation,Admin,Lead'])->group(function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    // Task detail (must come after /tasks/create)
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Task comments
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.comments.store');

    // Task attachments download
    Route::get('/attachments/{attachment}/download', [TaskController::class, 'downloadAttachment'])->name('attachments.download');

    // Update task status (for Kanban drag-and-drop)
    Route::post('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Lead routes - Task management (Lead and Admin only)
    Route::middleware(['role:Lead,Admin'])->group(function () {
        Route::post('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
        Route::post('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
        Route::get('/tasks/{task}/assign', [TaskController::class, 'assignForm'])->name('tasks.assign');
        Route::post('/tasks/{task}/assign', [TaskController::class, 'assignStore'])->name('tasks.assign.store');
    });

    // QA routes - Fail QA testing
    Route::middleware(['role:QA,Lead,Admin'])->group(function () {
        Route::post('/tasks/{task}/fail-qa', [TaskController::class, 'failQA'])->name('tasks.fail-qa');
    });

    // Implementation routes - Fail implementation testing
    Route::middleware(['role:Implementation,Lead,Admin'])->group(function () {
        Route::post('/tasks/{task}/fail-implementation', [TaskController::class, 'failImplementation'])->name('tasks.fail-implementation');
    });

    // Developer/QA/Lead/Admin - edit and update tasks
    Route::middleware(['role:Developer,QA,Lead,Admin'])->group(function () {
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    });

    // Admin routes - only Admin can access
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('sites', SiteController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('site-projects', SiteProjectController::class);

        // User password reset route (must be before resource)
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::resource('users', UserController::class);
    });
});
