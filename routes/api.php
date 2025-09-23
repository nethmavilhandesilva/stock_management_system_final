<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesEntryController;

Route::middleware(['auth:sanctum'])->group(function () { // or 'auth:api' depending on your setup
    // Dashboard initial data
    Route::get('/dashboard/initial-data', [DashboardController::class, 'getInitialData']);
    
    // GRN data
    Route::get('/grn-entry/{code}', [GrnEntryController::class, 'getGRNData']);
    
    // Customer loan amount
    Route::post('/customer/loan-amount', [CustomerController::class, 'getLoanAmount']);
    
    // Sales data
    Route::get('/sales', [SalesEntryController::class, 'create']);
});