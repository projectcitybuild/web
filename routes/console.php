<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')
    ->hourly();

Schedule::command('passport:purge')
    ->hourly();

Schedule::command('sitemap:generate')
    ->daily();

Schedule::command('cleanup:unactivated-accounts')
    ->weekly();

Schedule::command('donor-perks:expire')
    ->hourly();

Schedule::command('donor-perks:reward-currency')
    ->hourly();

Schedule::command('bans:expire')
    ->everyFifteenMinutes();

Schedule::command('backup:clean')
    ->dailyAt('00:00');

Schedule::command('backup:run')
    ->dailyAt('01:00');

Schedule::command('backup:monitor')
    ->dailyAt('02:00');
