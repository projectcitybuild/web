<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Tests\TestCase;

class AccountMinecraftAccountTest extends TestCase
{
    public function testMinecraftAccountShownInList()
    {
        $account = factory(Account::class)->create();
        $mcPlayer = factory(MinecraftPlayer::class)->create(['account_id' => $account->getKey()]);
        $mcPlayerAlias = factory(MinecraftPlayerAlias::class)->create(['player_minecraft_id' => $mcPlayer->getKey()]);

        $this->actingAs($account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid)
            ->assertSee($mcPlayerAlias->alias);
    }

    public function testMinecraftAccountWithoutAlias()
    {
        $account = factory(Account::class)->create();
        $mcPlayer = factory(MinecraftPlayer::class)->create(['account_id' => $account->getKey()]);

        $this->actingAs($account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($mcPlayer->uuid);
    }
}
