<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Bans\Models\GameBan;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PanelAccountTest extends TestCase
{
    use WithFaker;

    public function testAccountDetailsShown()
    {
        $this->withoutExceptionHandling();

        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.accounts.show', $account))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function testAccountDetailsChange()
    {
        $account = Account::factory()->create();

        $newData = [
            'email' => $this->faker->email,
            'username' => $this->faker->userName,
        ];

        $this->mock(SyncUserToDiscourse::class, function ($mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('syncAll')->once();
        });

        $this->actingAs($this->adminAccount())
            ->withoutExceptionHandling()
            ->put(route('front.panel.accounts.update', $account), $newData)
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', $newData);
    }

    public function testBanWithNoAliases()
    {
        $banningStaffAccount = Account::factory()->has(MinecraftPlayer::factory(), 'minecraftAccount')->create();
        $bannedPlayerAccount = Account::factory()
            ->has(MinecraftPlayer::factory()->count(1)
                ->has(GameBan::factory()->count(1)
                    ->bannedBy($banningStaffAccount->minecraftAccount->first())
                ), 'minecraftAccount')
            ->create();

        $this->withoutExceptionHandling();

        $resp = $this->actingAs($this->adminAccount())
            ->get(route('front.panel.accounts.show', $bannedPlayerAccount))
            ->assertOk()
            ->assertSee('No Alias');
    }
}
