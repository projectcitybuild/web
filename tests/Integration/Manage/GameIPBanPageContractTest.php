<?php

use App\Domains\Permissions\WebManagePermission;
use App\Http\Data\AdminGameIPBanData;
use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes IP ban list and edit pages through the IP ban DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::IP_BANS_VIEW->value,
        WebManagePermission::IP_BANS_EDIT->value,
    ]);
    $banner = MinecraftPlayer::factory()->create(['alias' => 'ContractIPBanner']);
    $ban = GameIPBan::factory()
        ->bannedBy($banner)
        ->create([
            'ip_address' => '192.0.2.15',
            'reason' => 'Contract IP reason',
            'created_at' => '2025-01-01 00:00:00',
        ]);

    $this->actingAs($manager)
        ->get(route('manage.ip-bans.edit', $ban))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('IPBans/IPBanEdit')
            ->where('ban.id', $ban->id)
            ->where('ban.banner_player.id', $banner->id)
            ->where('ban.ip_address', '192.0.2.15')
        );

    $banWithNullLoadedBanner = GameIPBan::factory()
        ->bannedBy($banner)
        ->create([
            'created_at' => '2025-01-02 00:00:00',
            'unbanned_at' => now(),
            'unban_type' => 'manual',
        ]);
    $banWithNullLoadedBanner->setRelation('bannerPlayer', null);

    $ipBanData = AdminGameIPBanData::fromModel($banWithNullLoadedBanner);

    expect($ipBanData->banner_player)->toBeNull()
        ->and($ipBanData->unban_type?->value)->toBe('manual');

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.ip-bans.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'IPBans/IPBanList',
        'X-Inertia-Partial-Data' => 'bans',
    ])
        ->get(route('manage.ip-bans.index'))
        ->assertOk()
        ->assertJsonPath('props.bans.data.1.id', $ban->id)
        ->assertJsonPath('props.bans.data.1.banner_player.alias', 'ContractIPBanner');
});
