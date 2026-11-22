<?php

use App\Http\Controllers\Api\Tasks\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('tasks', [TaskApiController::class, 'index']);
    Route::post('tasks', [TaskApiController::class, 'store']);
    Route::get('tasks/{task}', [TaskApiController::class, 'show']);
    Route::put('tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
    Route::patch('tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
});
