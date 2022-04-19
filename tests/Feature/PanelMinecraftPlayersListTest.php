<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Tests\TestCase;

class PanelMinecraftPlayersListTest extends TestCase
{
    public function testMCPlayerWithoutAccountShownOnList()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();
        $alias = $mcPlayer->aliases()->latest()->first();
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid)
            ->assertSee($alias->alias);
    }

    public function testMCPlayerWithAccountShownOnList()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->for($account)->hasAliases(1)->create();
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function testMCPlayerWithNoAlias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid);
    }
}
