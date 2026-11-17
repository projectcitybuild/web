<?php

use App\Http\Controllers\Admin\Auditing\ActivityController;
use App\Http\Controllers\Admin\Auditing\PlayerOpElevationController;
use Illuminate\Support\Facades\Route;
use Inertia\EncryptHistoryMiddleware;

Route::name('auditing.')
    ->prefix('auditing')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-auditing',
        'require-mfa',
        EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        Route::get('/', [ActivityController::class, 'index'])
            ->name('index');

        Route::resource('activity', ActivityController::class)
            ->only(['index']);

        Route::get('activity/audits/{audit}', [ActivityController::class, 'showAudit'])
            ->name('activity.audit.show');

        Route::get('op-elevations', [PlayerOpElevationController::class, 'index'])
            ->name('op-elevations.index');
    });
