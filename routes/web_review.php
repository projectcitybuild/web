<?php

use App\Http\Controllers\Review\BanAppeals\BanAppealController;
use App\Http\Controllers\Review\BuilderRanks\BuilderRanksController;
use App\Http\Controllers\Review\HomeController;
use Illuminate\Support\Facades\Route;

Route::name('review.')
    ->prefix('review')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-review',
        'require-mfa',
        Inertia\EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        Route::get('/', HomeController::class)
            ->name('index');

        Route::prefix('builder-ranks')->name('builder-ranks.')->group(function () {
            Route::get('/', [BuilderRanksController::class, 'index']);

            Route::prefix('{application}')->group(function () {
                Route::get('/', [BuilderRanksController::class, 'show'])->name('show');
                Route::post('approve', [BuilderRanksController::class, 'approve']);
                Route::post('deny', [BuilderRanksController::class, 'deny']);
            });
        });

        Route::resource('ban-appeals', BanAppealController::class)
            ->only('index', 'show', 'update');
    });
