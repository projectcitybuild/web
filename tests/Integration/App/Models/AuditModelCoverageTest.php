<?php

use App\Domains\BanAppeals\UseCases\CreateBanAppeal;
use App\Models\Account;
use App\Models\BuilderRankApplication;
use App\Models\MinecraftBuild;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftWarp;
use App\Models\Permission;
use App\Models\PlayerOpElevation;
use App\Models\Role;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('audits role attribute changes', function () {
    $role = Role::factory()->create(['name' => 'old-role']);

    $createdAudit = $role->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray(['name' => 'old-role']);

    $role->update(['name' => 'new-role']);

    $updatedAudit = $role->audits()->where('event', 'updated')->firstOrFail();
    expect($updatedAudit->old_values)->toMatchArray(['name' => 'old-role']);
    expect($updatedAudit->new_values)->toMatchArray(['name' => 'new-role']);
});

it('audits role permission changes through the manage endpoint', function () {
    $actor = $this->adminAccount();
    $role = Role::factory()->create(['name' => 'audited-role']);
    $permission = Permission::create(['name' => 'manage.audit.test']);

    $this->actingAs($actor)
        ->put('/manage/roles/'.$role->id.'/permissions', [
            'role_permission_ids' => [$permission->id],
        ])
        ->assertRedirect(route('manage.roles.index'));

    $audit = $role->audits()->where('event', 'sync')->firstOrFail();

    expect($role->permissions()->whereKey($permission->id)->exists())->toBeTrue();
    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->new_values)->toBe(['permission_names' => ['manage.audit.test']]);
});

it('audits warp changes', function () {
    $warp = MinecraftWarp::factory()->create(['name' => 'old-warp']);

    $createdAudit = $warp->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray(['name' => 'old-warp']);

    $warp->update(['name' => 'new-warp']);

    $updatedAudit = $warp->audits()->where('event', 'updated')->firstOrFail();
    expect($updatedAudit->old_values)->toMatchArray(['name' => 'old-warp']);
    expect($updatedAudit->new_values)->toMatchArray(['name' => 'new-warp']);
});

it('audits home changes and associates them with their player', function () {
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->create([
        'name' => 'old-home',
        'player_id' => $player->id,
    ]);

    $createdAudit = $home->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray([
        'name' => 'old-home',
        'player_id' => $player->id,
    ]);

    $home->update(['name' => 'new-home']);

    $updatedAudit = $home->audits()->where('event', 'updated')->firstOrFail();
    expect($updatedAudit->old_values)->toMatchArray(['name' => 'old-home']);
    expect($updatedAudit->new_values)->toMatchArray(['name' => 'new-home']);
});

it('audits build content while excluding high-volume vote changes', function () {
    $player = MinecraftPlayer::factory()->create();
    $build = MinecraftBuild::factory()->create([
        'name' => 'old-build',
        'player_id' => $player->id,
        'votes' => 5,
    ]);

    $createdAudit = $build->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray(['name' => 'old-build']);
    expect($createdAudit->new_values)->not->toHaveKey('votes');

    $build->update(['votes' => 6]);
    expect($build->audits()->count())->toBe(1);

    $build->update(['name' => 'new-build']);

    $updatedAudit = $build->audits()->where('event', 'updated')->firstOrFail();
    expect($updatedAudit->old_values)->toMatchArray(['name' => 'old-build']);
    expect($updatedAudit->new_values)->toMatchArray(['name' => 'new-build']);
});

it('audits player operator elevation changes', function () {
    $elevation = PlayerOpElevation::factory()->create();

    $createdAudit = $elevation->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray([
        'player_id' => $elevation->player_id,
        'reason' => $elevation->reason,
    ]);

    $elevation->update(['ended_at' => now()->addHour()]);

    expect($elevation->audits()->where('event', 'updated')->exists())->toBeTrue();
});

it('audits ban appeal creation but leaves decisions to explicit domain events', function () {
    Notification::fake();

    $appeal = app(CreateBanAppeal::class)->execute(
        banId: null,
        email: 'player@example.test',
        minecraftUuid: '12345678-1234-1234-1234-123456789012',
        dateOfBan: '2026-01-01',
        banReason: null,
        unbanReason: 'Appeal submitted',
        account: null,
    );

    $createdAudit = $appeal->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray(['email' => 'player@example.test']);

    $appeal->update(['status' => 2]);

    expect($appeal->audits()->count())->toBe(1);
});

it('audits builder application creation but leaves decisions to explicit domain events', function () {
    $account = Account::factory()->create();
    $application = BuilderRankApplication::factory()->create([
        'account_id' => $account->id,
        'minecraft_alias' => 'Builder',
    ]);

    $createdAudit = $application->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray([
        'account_id' => $account->id,
        'minecraft_alias' => 'Builder',
    ]);

    $application->update(['status' => 2]);

    expect($application->audits()->count())->toBe(1);
});
