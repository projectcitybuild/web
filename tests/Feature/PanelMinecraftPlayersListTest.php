<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use Tests\TestCase;

class PanelMinecraftPlayersListTest extends TestCase
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

    public function testMCPlayerWithoutAccountShownOnList()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();
        $alias = $mcPlayer->aliases()->latest()->first();
        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid)
            ->assertSee($alias->alias);
    }

    public function testMCPlayerWithAccountShownOnList()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->for($account)->hasAliases(1)->create();
        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function testMCPlayerWithNoAlias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid);
    }
}
