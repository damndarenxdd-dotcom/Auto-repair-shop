<?php

use App\Http\Controllers\Manager\ManagerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

    Route::get('/jobs', [ManagerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/create', [ManagerController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [ManagerController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [ManagerController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [ManagerController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{id}', [ManagerController::class, 'deleteJob'])->name('jobs.destroy');

    Route::get('/invoices', [ManagerController::class, 'invoices'])->name('invoices.index');
    Route::get('/jobs/{id}/invoice/create', [ManagerController::class, 'createInvoice'])->name('invoices.create');
    Route::post('/jobs/{id}/invoice', [ManagerController::class, 'storeInvoice'])->name('invoices.store');

    Route::get('/tasks', [ManagerController::class, 'taskDashboard'])->name('tasks.dashboard');
    Route::get('/tasks/history', [ManagerController::class, 'taskHistory'])->name('tasks.history');
    Route::get('/tasks/failures', [ManagerController::class, 'taskFailures'])->name('tasks.failures');
    Route::post('/tasks/failures/{id}/retry', [ManagerController::class, 'retryFailedJob'])->name('tasks.failures.retry');
});
