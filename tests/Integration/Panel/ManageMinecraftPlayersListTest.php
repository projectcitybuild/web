<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\MinecraftPlayer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ManageMinecraftPlayersListTest extends TestCase
{
    public function test_mc_player_without_account_shown_on_list()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.index'))
            ->assertOk()
            ->assertSee($player->uuid)
            ->assertSee($player->alias);
    }

    public function test_mc_player_with_account_shown_on_list()
    {
        $account = Account::factory()->create();
        MinecraftPlayer::factory()->for($account)->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.index'))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function test_mc_player_with_no_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.index'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid);
    }

    public function test_forbidden_unless_admin()
    {
        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.index'))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.minecraft-players.index'))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.minecraft-players.index'))
            ->assertForbidden();
    }
}
