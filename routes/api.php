<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EvaluatorController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Applications endpoints
    Route::prefix('applications')->group(function () {
        Route::post('/', [ApplicationController::class, 'store'])->name('applications.store');
        Route::post('/{id}/validate', [ApplicationController::class, 'validate'])->name('applications.validate');
        Route::post('/{id}/assign-evaluator', [ApplicationController::class, 'assignEvaluator'])->name('applications.assign-evaluator');
        Route::get('/consolidated', [ApplicationController::class, 'consolidated'])->name('applications.consolidated');
        Route::get('/{id}/summary', [ApplicationController::class, 'summary'])->name('applications.summary');
    });

    // Evaluators endpoints
    Route::prefix('evaluators')->group(function () {
        Route::get('/', [EvaluatorController::class, 'index'])->name('evaluators.index');
        Route::get('/{id}', [EvaluatorController::class, 'show'])->name('evaluators.show');
    });

    // Reports endpoints
    Route::prefix('reports')->group(function () {
        Route::post('/excel', [ReportController::class, 'generateExcel'])->name('reports.excel');
    });
});
