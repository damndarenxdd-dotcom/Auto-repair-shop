<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/weather', [AdminController::class, 'weather'])->name('weather');
    Route::get('/weather/preferences', [AdminController::class, 'getWeatherPreferences'])->name('weather.preferences.get');
    Route::post('/weather/preferences', [AdminController::class, 'saveWeatherPreferences'])->name('weather.preferences.save');

    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    Route::get('/services', [AdminController::class, 'services'])->name('services.index');
    Route::get('/services/create', [AdminController::class, 'createService'])->name('services.create');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::get('/services/{id}/edit', [AdminController::class, 'editService'])->name('services.edit');
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('services.destroy');

    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{id}/edit', [AdminController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [AdminController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{id}', [AdminController::class, 'deleteJob'])->name('jobs.destroy');

    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::get('/reports/revenue', [AdminController::class, 'revenueReport'])->name('reports.revenue');

    Route::get('/tasks', [AdminController::class, 'taskDashboard'])->name('tasks.dashboard');
    Route::get('/tasks/history', [AdminController::class, 'taskHistory'])->name('tasks.history');
    Route::get('/tasks/failures', [AdminController::class, 'taskFailures'])->name('tasks.failures');
    Route::post('/tasks/failures/{id}/retry', [AdminController::class, 'retryFailedJob'])->name('tasks.failures.retry');
});
