<?php

use App\Http\Controllers\Admin\Review\BanAppeals\BanAppealController;
use App\Http\Controllers\Admin\Review\BuilderRanks\BuilderRanksController;
use App\Http\Controllers\Admin\Review\HomeController;
use App\Http\Controllers\Admin\Review\PlayerReports\PlayerReportController as PlayerReportsController;
use Illuminate\Support\Facades\Route;
use Inertia\EncryptHistoryMiddleware;

Route::name('review.')
    ->prefix('review')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-review',
        'require-mfa',
        EncryptHistoryMiddleware::class,
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

        Route::get('player-reports', [PlayerReportsController::class, 'index'])
            ->name('player-reports.index');
        Route::get('player-reports/{playerReport}', [PlayerReportsController::class, 'show'])
            ->name('player-reports.show');
        Route::delete('player-reports/{playerReport}', [PlayerReportsController::class, 'destroy'])
            ->name('player-reports.destroy');
        Route::get('player-reports/{playerReport}/assignable-accounts', [PlayerReportsController::class, 'searchAssignableAccounts'])
            ->name('player-reports.assignable-accounts.search');
        Route::put('player-reports/{playerReport}/assignees', [PlayerReportsController::class, 'updateAssignees'])
            ->name('player-reports.assignees.update');
        Route::put('player-reports/{playerReport}/visibility', [PlayerReportsController::class, 'updateVisibility'])
            ->name('player-reports.visibility.update');
        Route::post('player-reports/{playerReport}/assignees/self', [PlayerReportsController::class, 'assignToSelf'])
            ->name('player-reports.assignees.self');
        Route::put('player-reports/{playerReport}/complete', [PlayerReportsController::class, 'complete'])
            ->name('player-reports.complete');
        Route::get('player-reports/{playerReport}/attachments/{attachment}', [PlayerReportsController::class, 'downloadAttachment'])
            ->name('player-reports.attachments.download');
        Route::get('player-reports/{playerReport}/attachments/{attachment}/preview', [PlayerReportsController::class, 'previewAttachment'])
            ->name('player-reports.attachments.preview');
    });
