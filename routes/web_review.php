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
    ->group(function() {
        Route::get('/', HomeController::class);

        Route::group([
            'prefix' => 'builder-ranks',
            'as' => 'builder-ranks.',
        ], function () {
            Route::get('/', [BuilderRanksController::class, 'index'])
                ->name('index');

            Route::get('{application}', [BuilderRanksController::class, 'show'])
                ->name('show');

            Route::post('{application}/approve', [BuilderRanksController::class, 'approve'])
                ->name('approve');

            Route::post('{application}/deny', [BuilderRanksController::class, 'deny'])
                ->name('deny');
        });

        Route::resource('ban-appeals', BanAppealController::class)
            ->only('index', 'show', 'update');
    });
