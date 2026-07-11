<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PdfCompressController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects — accessible to all roles (workspace)
    Route::resource('projects', ProjectController::class);

    // Tasks — all roles can create/update/view tasks
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Task Assignment — all roles can assign tasks to team members
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::delete('tasks/{task}/unassign/{user}', [TaskController::class, 'unassign'])->name('tasks.unassign');

    // Task Comments — all roles can comment on tasks
    Route::post('tasks/{task}/comments', [TaskController::class, 'comment'])->name('tasks.comments.store');
    Route::delete('task-comments/{comment}', [TaskController::class, 'destroyComment'])->name('tasks.comments.destroy');

    // Documents
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('tags', [DocumentController::class, 'storeTag'])->name('tags.store');

    // Compress PDF
    Route::get('/pdf-compress', [PdfCompressController::class, 'index'])->name('pdf-compress.index');
    Route::post('/pdf-compress', [PdfCompressController::class, 'compress'])->name('pdf-compress.compress');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Treasury & Payroll Hub
    Route::prefix('treasury')->name('treasury.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TreasuryController::class, 'dashboard'])->name('dashboard');
        Route::get('/omset', [\App\Http\Controllers\TreasuryController::class, 'inputOmset'])->name('omset');
        Route::post('/omset', [\App\Http\Controllers\TreasuryController::class, 'storeOmset'])->name('omset.store');
        Route::post('/omset/{id}/approve', [\App\Http\Controllers\TreasuryController::class, 'approveOmset'])->name('omset.approve');
        Route::delete('/omset/{id}', [\App\Http\Controllers\TreasuryController::class, 'destroyOmset'])->name('omset.destroy');
        
        Route::get('/payroll', [\App\Http\Controllers\TreasuryController::class, 'evaluasiPayroll'])->name('payroll');
        Route::post('/payroll/{id}/kpi', [\App\Http\Controllers\TreasuryController::class, 'updateKpi'])->name('payroll.kpi');
        Route::post('/payroll/{omsetLogId}/bayar', [\App\Http\Controllers\TreasuryController::class, 'bayarGaji'])->name('payroll.bayar');

        Route::get('/events', [\App\Http\Controllers\TreasuryController::class, 'events'])->name('events');
        Route::post('/events', [\App\Http\Controllers\TreasuryController::class, 'storeEvent'])->name('events.store');
        Route::post('/events/{eventId}/expenses', [\App\Http\Controllers\TreasuryController::class, 'storeExpense'])->name('events.expense.store');

        Route::get('/cashbook', [\App\Http\Controllers\TreasuryController::class, 'cashBook'])->name('cashbook');
        Route::post('/cashbook', [\App\Http\Controllers\TreasuryController::class, 'storeCash'])->name('cashbook.store');

        Route::get('/users', [\App\Http\Controllers\TreasuryController::class, 'users'])->name('users');
        Route::post('/users', [\App\Http\Controllers\TreasuryController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\TreasuryController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\TreasuryController::class, 'destroyUser'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';

