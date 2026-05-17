<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstimacionPesoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('estimacion')->group(function () {
    Route::get('/health', [EstimacionPesoController::class, 'healthCheck']);
    Route::post('/estimar', [EstimacionPesoController::class, 'estimar']);
    Route::post('/estimar-batch', [EstimacionPesoController::class, 'estimarBatch']);
});