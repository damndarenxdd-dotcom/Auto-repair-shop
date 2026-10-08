<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role?->name;
        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'mechanic' => redirect()->route('mechanic.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return view('welcome');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Live weather JSON endpoint used by dashboard polling
    Route::get('/weather', [AdminController::class, 'weather'])->name('weather');
    // Weather preferences (per-user)
    Route::get('/weather/preferences', [AdminController::class, 'getWeatherPreferences'])->name('weather.preferences.get');
    Route::post('/weather/preferences', [AdminController::class, 'saveWeatherPreferences'])->name('weather.preferences.save');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    
    // Services Management
    Route::get('/services', [AdminController::class, 'services'])->name('services.index');
    Route::get('/services/create', [AdminController::class, 'createService'])->name('services.create');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::get('/services/{id}/edit', [AdminController::class, 'editService'])->name('services.edit');
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('services.destroy');
    
    // Jobs Management
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{id}/edit', [AdminController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [AdminController::class, 'updateJob'])->name('jobs.update');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::get('/reports/revenue', [AdminController::class, 'revenueReport'])->name('reports.revenue');
});

// Manager Routes
Route::middleware(['auth', 'verified', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    
    // Jobs Management
    Route::get('/jobs', [ManagerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/create', [ManagerController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [ManagerController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [ManagerController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [ManagerController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{id}', [ManagerController::class, 'deleteJob'])->name('jobs.destroy');
    
    // Invoices
    Route::get('/invoices', [ManagerController::class, 'invoices'])->name('invoices.index');
    Route::get('/jobs/{id}/invoice/create', [ManagerController::class, 'createInvoice'])->name('invoices.create');
    Route::post('/jobs/{id}/invoice', [ManagerController::class, 'storeInvoice'])->name('invoices.store');
});

// Mechanic Routes
Route::middleware(['auth', 'verified', 'role:mechanic'])->prefix('mechanic')->name('mechanic.')->group(function () {
    Route::get('/dashboard', [MechanicController::class, 'dashboard'])->name('dashboard');
    
    // Jobs Management
    Route::get('/jobs', [MechanicController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{id}', [MechanicController::class, 'viewJob'])->name('jobs.show');
    Route::put('/jobs/{id}/status', [MechanicController::class, 'updateJobStatus'])->name('jobs.updateStatus');
    Route::post('/jobs/{id}/notes', [MechanicController::class, 'addNotes'])->name('jobs.addNotes');
});

// Customer Routes
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    
    // Jobs Management
    Route::get('/jobs', [CustomerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{id}', [CustomerController::class, 'viewJob'])->name('jobs.show');
    Route::get('/jobs/request/new', [CustomerController::class, 'requestJob'])->name('jobs.request');
    Route::post('/jobs/request', [CustomerController::class, 'submitJobRequest'])->name('jobs.submit');
    
    // Notifications
    Route::get('/notifications', [CustomerController::class, 'notifications'])->name('notifications.index');
    Route::put('/notifications/{id}/read', [CustomerController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerController::class, 'markAllAsRead'])->name('notifications.readAll');
    
    // Invoices
    Route::get('/invoices', [CustomerController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{id}', [CustomerController::class, 'viewInvoice'])->name('invoices.show');
    Route::get('/invoices/{id}/download', [CustomerController::class, 'downloadInvoice'])->name('invoices.download');
});

require __DIR__.'/auth.php';

// Authenticated endpoints for live weather (usable by all roles)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/live-weather', [AdminController::class, 'weather'])->name('weather.live');
    Route::get('/weather-preferences', [AdminController::class, 'getWeatherPreferences'])->name('weather.preferences.get');
    Route::post('/weather-preferences', [AdminController::class, 'saveWeatherPreferences'])->name('weather.preferences.save');
});
