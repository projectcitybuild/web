<?php

namespace Tests\Integration\Panel;

use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Core\Domains\Mojang\Data\MojangPlayer;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class ManageMinecraftPlayerLookupTest extends TestCase
{
    use WithFaker;

    public function test_lookup_player_by_undashed_uuid()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => $mcPlayer->uuid,
            ])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_dashed_uuid()
    {
        $uuid = $this->faker->uuid;
        $mcPlayer = MinecraftPlayer::factory()->create([
            'uuid' => str_replace('-', '', $uuid),
        ]);

        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => $uuid,
            ])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_stored_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => $mcPlayer->alias,
            ])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_player_by_non_stored_alias()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) use ($mcPlayer) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(
                new MojangPlayer($mcPlayer->uuid, 'Herobrine')
            );
        });

        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function test_lookup_of_invalid_player()
    {
        // this is a player that does not exist in Mojang's database
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(null);
        });

        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('manage.minecraft-players.index'));
    }

    public function test_lookup_of_unknown_player()
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(
                new MojangPlayer('f84c6a790a4e45e0879bcd49ebd4c4e2', 'Herobrine')
            );
        });

        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('manage.minecraft-players.index'));
    }

    public function test_empty_lookup_string_entered()
    {
        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => '',
            ])
            ->assertRedirect(route('manage.minecraft-players.index'));
    }

    public function test_null_lookup_string_entered()
    {
        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'), [
                'query' => null,
            ])
            ->assertRedirect(route('manage.minecraft-players.index'));
    }

    public function test_forbidden_unless_admin()
    {
        $this->actingAs($this->adminAccount())
            ->post(route('manage.minecraft-players.lookup'))
            ->assertRedirect();

        $this->actingAs($this->staffAccount())
            ->post(route('manage.minecraft-players.lookup'))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->post(route('manage.minecraft-players.lookup'))
            ->assertForbidden();
    }
}
