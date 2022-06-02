<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Tests\TestCase;

class PanelMinecraftPlayerEditTest extends TestCase
{
    public function testCanViewEditForm()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount())
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
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.edit', $mcPlayer))
            ->assertOk();
    }

    public function testCanChangeAssignedUser()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $anotherAccount = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => $anotherAccount->account_id])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));

        $this->assertDatabaseHas('players_minecraft', [
            'player_minecraft_id' => $mcPlayer->player_minecraft_id,
            'account_id' => $anotherAccount->account_id,
        ]);
    }

    public function testCanChangeToNoUser()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $this->actingAs($this->adminAccount())
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => null])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testCantChangeUserToUnassigned()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($this->adminAccount())
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => 12])
            ->assertSessionHasErrors();
    }
}
