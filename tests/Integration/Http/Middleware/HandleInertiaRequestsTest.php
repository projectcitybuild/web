<?php

use App\Http\Middleware\MfaAuthenticated;
use App\Http\Middleware\RequireMfaEnabled;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config([
        'app.version' => '1.8.0',
        'app.commit' => '0123456789abcdef0123456789abcdef01234567',
        'sentry.environment' => 'staging',
    ]);
});

it('shares the authenticated account name and avatar with admin areas', function (string $routeName) {
    $account = Account::factory()->create([
        'username' => 'ReviewUser',
    ]);
    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->id,
    ]);

    Gate::before(fn () => true);
    config(['inertia.ssr.enabled' => false]);
    $this->withoutMiddleware([
        MfaAuthenticated::class,
        RequireMfaEnabled::class,
    ]);

    $this->actingAs($account)
        ->get(route($routeName))
        ->assertOk()
        ->assertViewIs('admin.app')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('appVersion', '1.8.0')
            ->where('commitSha', '0123456789abcdef0123456789abcdef01234567')
            ->where('environment', 'staging')
            ->where('account', [
                'name' => 'ReviewUser',
                'avatarUuid' => $player->uuid,
            ])
        );
})->with([
    'manage' => 'manage.index',
    'review' => 'review.index',
]);

it('shares the authenticated account name and avatar with the auditing area', function () {
    $account = $this->adminAccount();
    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->id,
    ]);

    Gate::before(fn () => true);
    config(['inertia.ssr.enabled' => false]);
    $this->withoutMiddleware([
        MfaAuthenticated::class,
        RequireMfaEnabled::class,
    ]);

    $this->actingAs($account)
        ->get(route('auditing.index'))
        ->assertOk()
        ->assertViewIs('admin.app')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Activity/AuditList')
            ->where('appVersion', '1.8.0')
            ->where('commitSha', '0123456789abcdef0123456789abcdef01234567')
            ->where('environment', 'staging')
            ->where('account', [
                'name' => $account->username,
                'avatarUuid' => $player->uuid,
            ])
        );
});
