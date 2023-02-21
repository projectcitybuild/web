<?php

namespace Tests\Integration\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PanelGroupScope;
use Illuminate\Foundation\Testing\WithFaker;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayer;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\IntegrationTestCase;

class PanelMinecraftPlayerLookupTest extends IntegrationTestCase
{
    use WithFaker;

    private Account $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    public function test_lookup_player_by_undashed_uuid()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $mcPlayer->uuid,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_dashed_uuid()
    {
        $uuid = $this->faker->uuid;
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create([
            'uuid' => str_replace('-', '', $uuid),
        ]);

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $uuid,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_stored_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();
        $alias = $mcPlayer->aliases()->latest()->first();

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $alias->alias,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_non_stored_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) use ($mcPlayer) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(
                new MojangPlayer($mcPlayer->uuid, 'Herobrine')
            );
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_of_invalid_player()
    {
        // this is a player that does not exist in Mojang's database
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(null);
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.index'));
    }

    public function test_lookup_of_unknown_player()
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(
                new MojangPlayer('f84c6a790a4e45e0879bcd49ebd4c4e2', 'Herobrine')
            );
        });

        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.index'));
    }

    public function test_empty_lookup_string_entered()
    {
        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => '',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.index'));
    }

    public function test_null_lookup_string_entered()
    {
        $this->actingAs($this->admin)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => null,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.index'));
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.lookup'))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        // No idea why this is needed...
        $this->expectException(HttpException::class);

        $this->actingAs($admin)
            ->post(route('front.panel.minecraft-players.lookup'))
            ->assertUnauthorized();
    }
}
