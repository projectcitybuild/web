<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\Badge;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes badge list and edit pages through the badge DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::BADGES_VIEW->value,
        WebManagePermission::BADGES_EDIT->value,
    ]);
    $badge = Badge::factory()->create(['display_name' => 'Contract Badge']);
    $account = Account::factory()->create();
    $account->badges()->attach($badge);

    $this->actingAs($manager)
        ->get(route('manage.badges.edit', $badge))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Badges/BadgeEdit')
            ->where('badge.id', $badge->id)
            ->where('badge.display_name', 'Contract Badge')
            ->missing('badge.pivot')
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.badges.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Badges/BadgeList',
        'X-Inertia-Partial-Data' => 'badges',
    ])
        ->get(route('manage.badges.index'))
        ->assertOk()
        ->assertJsonPath('props.badges.data.0.id', $badge->id)
        ->assertJsonPath('props.badges.data.0.display_name', 'Contract Badge')
        ->assertJsonPath('props.badges.data.0.accounts_count', 1);
});

it('lists only accounts assigned a badge', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::BADGES_VIEW->value,
    ]);
    $badge = Badge::factory()->create(['display_name' => 'Members Badge']);
    $account = Account::factory()->create(['username' => 'BadgeHolder']);
    $account->badges()->attach($badge);
    Account::factory()->create(['username' => 'OtherAccount']);

    $this->actingAs($manager)
        ->get(route('manage.badges.accounts', $badge))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Badges/BadgeAccountList')
            ->where('badge.id', $badge->id)
            ->where('accounts.data.0.id', $account->id)
            ->where('accounts.data.0.username', 'BadgeHolder')
            ->where('accounts.total', 1)
        );
});
