<?php

use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

    Route::get('/jobs', [CustomerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/request', [CustomerController::class, 'requestJob'])->name('jobs.request');
    Route::post('/jobs/request', [CustomerController::class, 'submitJobRequest'])->name('jobs.submit');
    Route::get('/jobs/{id}', [CustomerController::class, 'viewJob'])->name('jobs.show');
    Route::get('/jobs/{id}/json', [CustomerController::class, 'jobJson'])->name('jobs.json');

    Route::get('/notifications', [CustomerController::class, 'notifications'])->name('notifications.index');
    Route::put('/notifications/{id}/read', [CustomerController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('/invoices', [CustomerController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{id}', [CustomerController::class, 'viewInvoice'])->name('invoices.show');
    Route::get('/invoices/{id}/download', [CustomerController::class, 'downloadInvoice'])->name('invoices.download');
});
