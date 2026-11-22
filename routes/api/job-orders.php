<?php

use App\Http\Controllers\Api\JobOrders\JobOrderApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('job-orders', JobOrderApiController::class)->parameters(['job-orders' => 'repairJob']);
    Route::patch('job-orders/{repairJob}/services', [JobOrderApiController::class, 'syncServices'])->name('job-orders.services.sync');
});
