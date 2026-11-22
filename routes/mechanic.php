<?php

use App\Http\Controllers\Mechanic\MechanicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:mechanic'])->prefix('mechanic')->name('mechanic.')->group(function () {
    Route::get('/dashboard', [MechanicController::class, 'dashboard'])->name('dashboard');

    Route::get('/jobs', [MechanicController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{id}', [MechanicController::class, 'viewJob'])->name('jobs.show');
    Route::put('/jobs/{id}/status', [MechanicController::class, 'updateJobStatus'])->name('jobs.updateStatus');
    Route::post('/jobs/{id}/notes', [MechanicController::class, 'addNotes'])->name('jobs.addNotes');
});
