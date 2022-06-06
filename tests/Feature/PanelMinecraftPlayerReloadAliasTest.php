<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PanelGroupScope;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayerNameHistory;
use Mockery\MockInterface;
use Tests\E2ETestCase;

class PanelMinecraftPlayerReloadAliasTest extends E2ETestCase
{
    private Account $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    public function test_reload_alias()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(
                new MojangPlayerNameHistory([
                    (object) ['name' => 'Original'],
                    (object) ['name' => 'Second', 'changeToAt' => 1423063907000],
                    (object) ['name' => 'Third', 'changeToAt' => 1425714026000],
                ])
            );
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer));

        $this->assertDatabaseHas('players_minecraft_aliases', [
            'player_minecraft_id' => $minecraftPlayer->player_minecraft_id,
            'alias' => 'Third',
        ]);
    }

    public function test_reload_with_no_alias_change()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1, ['alias' => 'MyAlias'])->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(
                new MojangPlayerNameHistory([
                    (object) ['name' => 'MyAlias'],
                ])
            );
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer));

        $this->assertDatabaseCount('players_minecraft_aliases', 1);
    }

    public function test_forbidden_without_scope()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer))
            ->assertForbidden();
    }

    public function test_forbidden_without_panel_access()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer))
            ->assertForbidden();
    }
}
