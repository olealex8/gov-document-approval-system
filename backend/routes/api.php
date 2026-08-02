<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DocumentFileController;
use App\Http\Controllers\Api\DocumentRequestController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // DOCUMENT REQUEST
    Route::get('/document-requests', [DocumentRequestController::class, 'index']);
    Route::get('/document-requests/{documentRequest}', [DocumentRequestController::class, 'show']);
    Route::post('/document-requests', [DocumentRequestController::class, 'store']);
    Route::patch('/document-requests/{documentRequest}/decide', [DocumentRequestController::class, 'decide']);
    Route::patch('/document-requests/{documentRequest}/resubmit', [DocumentRequestController::class, 'resubmit']);

    // DOCUMENT FILE
    Route::post('/document-requests/{documentRequest}/files', [DocumentFileController::class, 'store']);
    Route::delete('/document-files/{documentFile}', [DocumentFileController::class, 'destroy']);

    // DASHBOARD
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

    // EXPORT
    Route::get('/export/excel', [ExportController::class, 'excel']);

    // USER
    Route::get('/users', [UserController::class, 'index']);
    
});