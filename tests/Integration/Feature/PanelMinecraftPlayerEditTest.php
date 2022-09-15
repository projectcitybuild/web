<?php

namespace Tests\Integration\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PanelGroupScope;
use Tests\IntegrationTestCase;

class PanelMinecraftPlayerEditTest extends IntegrationTestCase
{
    private Account $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    public function test_can_view_edit_form()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.edit', $mcPlayer))
            ->assertOk()
            ->assertSee('Edit');
    }

    /**
     * @ticket 596
     */
    public function test_can_view_edit_form_with_active_account()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory())->create();
        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.edit', $mcPlayer))
            ->assertOk();
    }

    public function test_can_change_assigned_user()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $anotherAccount = Account::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => $anotherAccount->account_id])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));

        $this->assertDatabaseHas('players_minecraft', [
            'player_minecraft_id' => $mcPlayer->player_minecraft_id,
            'account_id' => $anotherAccount->account_id,
        ]);
    }

    public function test_can_change_to_no_user()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $this->actingAs($this->admin)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => null])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function test_cant_change_user_to_unassigned()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($this->admin)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => 12])
            ->assertSessionHasErrors();
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($admin)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($admin)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer))
            ->assertUnauthorized();
    }
}
