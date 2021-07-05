<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use Tests\TestCase;

class PanelMinecraftPlayerEditTest extends TestCase
{
    private $adminAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminAccount = Account::factory()->create();

        $adminGroup = Group::create([
            'name' => 'Administrator',
            'can_access_panel' => true,
        ]);

        $this->adminAccount->groups()->attach($adminGroup->group_id);
    }

    public function testCanViewEditForm()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.minecraft-players.edit', $mcPlayer))
            ->assertOk()
            ->assertSee('Edit');
    }

    public function testCanChangeAssignedUser()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();
        $anotherAccount = Account::factory()->create();

        $this->actingAs($this->adminAccount)
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
        $this->actingAs($this->adminAccount)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => null])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testCantChangeUserToUnassigned()
    {
        $mcPlayer = MinecraftPlayer::factory()->for(Account::factory()->create())->create();

        $this->actingAs($this->adminAccount)
            ->put(route('front.panel.minecraft-players.update', $mcPlayer), ['account_id' => 12])
            ->assertSessionHasErrors();
    }
}
