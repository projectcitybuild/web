<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\PlayerWarning;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes warning list and edit pages through the warning DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::WARNINGS_VIEW->value,
        WebManagePermission::WARNINGS_EDIT->value,
    ]);
    $warning = PlayerWarning::factory()
        ->withPlayers()
        ->create(['reason' => 'Contract warning']);
    $warning->load('warnedPlayer', 'warnerPlayer');

    $this->actingAs($manager)
        ->get(route('manage.warnings.edit', $warning))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Warnings/WarningEdit')
            ->where('warning.id', $warning->id)
            ->where('warning.warned_player.id', $warning->warned_player_id)
            ->where('warning.warner_player.id', $warning->warner_player_id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.warnings.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Warnings/WarningList',
        'X-Inertia-Partial-Data' => 'warnings',
    ])
        ->get(route('manage.warnings.index'))
        ->assertOk()
        ->assertJsonPath('props.warnings.data.0.id', $warning->id)
        ->assertJsonPath('props.warnings.data.0.warned_player.id', $warning->warned_player_id)
        ->assertJsonPath('props.warnings.data.0.warner_player.id', $warning->warner_player_id);
});
