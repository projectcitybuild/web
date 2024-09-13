<?php

use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')
    ->everyFifteenMinutes();

Schedule::command('passport:purge')
    ->hourly();

Schedule::command('sitemap:generate')
    ->daily();

Schedule::command('donor-perks:expire')
    ->hourly();

Schedule::command('donor-perks:reward-currency')
    ->hourly();

Schedule::command('backup:clean')
    ->dailyAt('00:00');

Schedule::command('backup:run')
    ->dailyAt('01:00');

Schedule::command('backup:monitor')
    ->dailyAt('02:00');

Artisan::command('bans:prune', function () {
    GamePlayerBan::whereNull('unbanned_at')
        ->whereDate('expires_at', '<=', now())
        ->update([
            'unbanned_at' => now(),
            'unban_type' => UnbanType::EXPIRED->value,
        ]);
})->everyFifteenMinutes();
