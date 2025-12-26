<?php

use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppReminderNotification;
use App\Models\Account;
use App\Models\BuilderRankApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\Support\TemporaryConfig;

beforeEach(function () {
    Notification::fake();
    Carbon::setTestNow(now());
});

afterEach(function () {
    Carbon::setTestNow();
});

it('sends reminders for open builder rank applications that are due', function () {
    Config::set('discord.webhook_architect_channel', 'https://example.com/webhook');

    $account = Account::factory()->create();
    $openApps = BuilderRankApplication::factory()
        ->for($account)
        ->count(3)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->subDay(),
        ]);

    $closedApp = BuilderRankApplication::factory()
        ->for($account)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->addDay(),
        ]);

    Artisan::call('build-rank-apps:remind');

    Notification::assertSentOnDemand(
        BuilderRankAppReminderNotification::class,
        function ($notification, $channels, $notifiable) use ($openApps) {
            expect($channels)->toContain('discord');
            expect($notification->openApps->pluck('id')->sort()->values())
                ->toEqual($openApps->pluck('id')->sort()->values());

            return true;
        }
    );
});

it('increments next_reminder_at for open apps', function () {
    Config::set('discord.webhook_architect_channel', 'https://example.com/webhook');

    $account = Account::factory()->create();
    $openApps = BuilderRankApplication::factory()
        ->for($account)
        ->count(3)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->subDay(),
        ]);

    $closedApp = BuilderRankApplication::factory()
        ->for($account)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->addDay(),
        ]);

    Artisan::call('build-rank-apps:remind');

    $openApps->each(function ($app) {
        $app->refresh();
        expect($app->next_reminder_at)->toEqual(now()->addDays(3));
    });

    $closedApp->refresh();
    expect($closedApp->next_reminder_at)->toEqual(now()->addDay());
});

it('does nothing when no applications are due', function () {
    Config::set('discord.webhook_architect_channel', 'https://example.com/webhook');

    $account = Account::factory()->create();
    BuilderRankApplication::factory()
        ->for($account)
        ->count(2)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->addDays(2),
        ]);

    Artisan::call('build-rank-apps:remind');

    Notification::assertNothingSent();
});

it('limits reminders to 10 applications', function () {
    Config::set('discord.webhook_architect_channel', 'https://example.com/webhook');

    $account = Account::factory()->create();
    BuilderRankApplication::factory()
        ->for($account)
        ->count(15)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->subMinute(),
        ]);

    Artisan::call('build-rank-apps:remind');

    Notification::assertSentOnDemand(
        BuilderRankAppReminderNotification::class,
        function ($notification) {
            expect($notification->openApps)->toHaveCount(10);
            return true;
        }
    );
});

it('throws an exception when the discord channel is not configured', function () {
    Config::set('discord.webhook_architect_channel', null);

    $account = Account::factory()->create();
    BuilderRankApplication::factory()
        ->for($account)
        ->create([
            'closed_at' => null,
            'next_reminder_at' => now()->subDay(),
        ]);

    expect(fn () => Artisan::call('build-rank-apps:remind'))
        ->toThrow(Exception::class);

    Notification::assertNothingSent();
});
