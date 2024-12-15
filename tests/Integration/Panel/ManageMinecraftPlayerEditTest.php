<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\MinecraftPlayer;
use Tests\TestCase;

class ManageMinecraftPlayerEditTest extends TestCase
{
    public function test_can_view_edit_form()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.edit', $mcPlayer))
            ->assertOk()
            ->assertSee('Edit');
    }

    /**
     * @ticket 596
     */
    public function test_can_view_edit_form_with_active_account()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory())->create();
        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.edit', $mcPlayer))
            ->assertOk();
    }

    public function test_can_change_assigned_user()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $anotherAccount = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->put(route('manage.minecraft-players.update', $mcPlayer), ['account_id' => $anotherAccount->account_id])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));

        $this->assertDatabaseHas('players_minecraft', [
            'player_minecraft_id' => $mcPlayer->player_minecraft_id,
            'account_id' => $anotherAccount->account_id,
        ]);
    }

    public function test_can_change_to_no_user()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $this->actingAs($this->adminAccount())
            ->put(route('manage.minecraft-players.update', $mcPlayer), ['account_id' => null])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function test_cant_change_user_to_unassigned()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($this->adminAccount())
            ->put(route('manage.minecraft-players.update', $mcPlayer), ['account_id' => 12])
            ->assertSessionHasErrors();
    }

    public function test_forbidden_unless_admin()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($this->adminAccount())
            ->put(route('manage.minecraft-players.update', $mcPlayer))
            ->assertRedirect();

        $this->actingAs($this->staffAccount())
            ->put(route('manage.minecraft-players.update', $mcPlayer))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->put(route('manage.minecraft-players.update', $mcPlayer))
            ->assertForbidden();
    }
}
