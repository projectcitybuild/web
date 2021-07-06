<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayerNameHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class PanelCreateMinecraftPlayerTest extends TestCase
{
    use WithFaker;

    private function mockSuccessfulUuidCheck(): void
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            // API will return null for name history if the player does not exist
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(new MojangPlayerNameHistory([
                (object) ['name' => 'Herobrine'],
            ]));
        });
    }

    public function testCanViewCreateForm()
    {
        $this->markTestSkipped();
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.create'))
            ->assertOk();
    }

    public function testCanAddByUndashedUuidToAccount()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->make();

        $this->mockSuccessfulUuidCheck();

        $this->actingAs($this->adminAccount())
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $mcPlayer->uuid,
                'account_id' => $account->account_id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $mcPlayer->uuid,
            'account_id' => $account->account_id,
        ]);
    }

    public function testCanAddDashedUuidToAccount()
    {
        $account = Account::factory()->create();
        $dashedUUID = $this->faker->uuid;

        $this->mockSuccessfulUuidCheck();

        $this->actingAs($this->adminAccount())
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $dashedUUID,
                'account_id' => $account->account_id,
            ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => str_replace('-', '', $dashedUUID),
            'account_id' => $account->account_id,
        ]);
    }

    public function testAssignsExistingPlayerToAccount()
    {
        $oldAccount = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->for($oldAccount)->create();
        $newAccount = Account::factory()->create();

        $this->mockSuccessfulUuidCheck();

        $this->actingAs($this->adminAccount())
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $mcPlayer->uuid,
                'account_id' => $newAccount->account_id,
            ]);

        $this->assertDatabaseCount('players_minecraft', 1);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $mcPlayer->uuid,
            'account_id' => $newAccount->account_id,
        ]);
    }

    public function testFakeUUIDDoesNotWork()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->make();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            // API will return null for name history if the player does not exist
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(null);
        });

        $this->actingAs($this->adminAccount())
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $mcPlayer->uuid,
                'account_id' => $account->account_id,
            ]);

        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => $mcPlayer->uuid,
            'account_id' => $account->account_id,
        ]);
    }
}
