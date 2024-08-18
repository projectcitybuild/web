<?php

namespace Tests\Integration\Feature;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\IntegrationTestCase;

class PanelMinecraftPlayersListTest extends IntegrationTestCase
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

    public function test_mc_player_without_account_shown_on_list()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();
        $alias = $mcPlayer->aliases()->latest()->first();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid)
            ->assertSee($alias->alias);
    }

    public function test_mc_player_with_account_shown_on_list()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->for($account)->hasAliases(1)->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function test_mc_player_with_no_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid);
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertUnauthorized();
    }
}
