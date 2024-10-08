<?php

namespace Panel;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\IntegrationTestCase;

class PanelMinecraftPlayerShowTest extends IntegrationTestCase
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

    public function test_ban_shown()
    {
        $banningStaff = MinecraftPlayer::factory()->hasAliases(1)->create();
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GamePlayerBan::factory()->bannedBy($banningStaff))
            ->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GamePlayerBan::first()->reason);
    }

    public function test_ban_with_null_staff_shown()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GamePlayerBan::factory()->bannedByConsole())
            ->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GamePlayerBan::first()->reason);
    }

    public function test_unauthorised_without_scope()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GamePlayerBan::factory()->bannedByConsole())
            ->create();

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GamePlayerBan::factory()->bannedByConsole())
            ->create();

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertUnauthorized();
    }
}
