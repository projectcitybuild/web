<?php

use App\Core\Utilities\Redact;
use App\Domains\Permissions\WebReviewPermission;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson as Assert;

it('renders loaded ban appeal data and a redacted linked account', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = Account::factory()->hasFinishedTotp()->create();
    $role = Role::factory()->staff()->create();
    $role->permissions()->attach(Permission::create([
        'name' => WebReviewPermission::BAN_APPEALS_VIEW->value,
    ])->id);
    $reviewer->roles()->attach($role->id);

    $account = Account::factory()->create(['email' => 'private@example.com']);
    $banAppeal = BanAppeal::factory()->for($account)->create([
        'created_at' => '2025-01-02 03:04:05',
        'decided_at' => null,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.ban-appeals.show', $banAppeal))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BanAppeals/BanAppealShow')
            ->where('banAppeal.id', $banAppeal->id)
            ->where('banAppeal.account.email', Redact::email('private@example.com'))
            ->where('banAppeal.game_player_ban', null)
            ->where('banAppeal.decider_player', null)
            ->where('banAppeal.created_at', '2025-01-02T03:04:05.000000Z')
            ->etc()
        );
});
