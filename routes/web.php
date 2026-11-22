<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role?->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'mechanic' => redirect()->route('mechanic.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect()->route('login'),
        };
    }

    return view('welcome');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

require __DIR__.'/admin.php';
require __DIR__.'/manager.php';
require __DIR__.'/mechanic.php';
require __DIR__.'/customer.php';
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/live-weather', [\App\Http\Controllers\Admin\AdminController::class, 'weather'])->name('weather.live');
    Route::get('/weather-preferences', [\App\Http\Controllers\Admin\AdminController::class, 'getWeatherPreferences'])->name('weather.preferences.get');
    Route::post('/weather-preferences', [\App\Http\Controllers\Admin\AdminController::class, 'saveWeatherPreferences'])->name('weather.preferences.save');
});
