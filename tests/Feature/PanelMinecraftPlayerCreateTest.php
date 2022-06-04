<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PanelGroupScope;
use Illuminate\Foundation\Testing\WithFaker;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayerNameHistory;
use Mockery\MockInterface;
use Tests\E2ETestCase;

class PanelMinecraftPlayerCreateTest extends E2ETestCase
{
    use WithFaker;

    private Account $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    private function mockSuccessfulUUIDCheck(): void
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            // API will return null for name history if the player does not exist
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(new MojangPlayerNameHistory([
                (object) ['name' => 'Herobrine'],
            ]));
        });
    }

    public function test_can_view_create_form()
    {
        $this->actingAs($this->admin)
            ->get(route('front.panel.minecraft-players.create'))
            ->assertOk();
    }

    public function test_can_add_by_undashed_uuid_to_account()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->make();

        $this->mockSuccessfulUUIDCheck();

        $this->actingAs($this->admin)
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

    public function test_can_add_dashed_uuid_to_account()
    {
        $account = Account::factory()->create();
        $dashedUUID = $this->faker->uuid;

        $this->mockSuccessfulUUIDCheck();

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $dashedUUID,
                'account_id' => $account->account_id,
            ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => str_replace('-', '', $dashedUUID),
            'account_id' => $account->account_id,
        ]);
    }

    public function test_assigns_existing_player_to_account()
    {
        $oldAccount = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->for($oldAccount)->create();
        $newAccount = Account::factory()->create();

        $this->mockSuccessfulUUIDCheck();

        $this->actingAs($this->admin)
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

    public function test_fake_uuid_does_not_work()
    {
        $account = Account::factory()->create();
        $mcPlayer = MinecraftPlayer::factory()->make();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            // API will return null for name history if the player does not exist
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(null);
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.store'), [
                'uuid' => $mcPlayer->uuid,
                'account_id' => $account->account_id,
            ]);

        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => $mcPlayer->uuid,
            'account_id' => $account->account_id,
        ]);
    }

    public function test_forbidden_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.store'))
            ->assertForbidden();
    }

    public function test_forbidden_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.store'))
            ->assertForbidden();
    }
}
