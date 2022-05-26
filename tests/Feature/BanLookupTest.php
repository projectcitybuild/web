<?php

namespace Feature;

use App\Exceptions\Http\TooManyRequestsException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Foundation\Testing\WithFaker;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayer;
use Mockery\MockInterface;
use Tests\TestCase;

class BanLookupTest extends TestCase
{
    use WithFaker;

    private function mockMojangToReturn($username, $uuid)
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) use ($uuid, $username) {
            $mock->shouldReceive('getUuidOf')
                ->with($username)
                ->once()->andReturn(
                new MojangPlayer($uuid, $username)
            );
        });
    }

    public function test_can_submit_lookup_as_guest()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()->active()->for($mcPlayer, 'bannedPlayer')->create();

        $this->mockMojangToReturn('Herobrine', $mcPlayer->uuid);

        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.create', $ban));
    }

    public function test_can_submit_lookup_as_user()
    {
        $this->actingAs(Account::factory()->create());
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()->active()->for($mcPlayer, 'bannedPlayer')->create();

        $this->mockMojangToReturn('Herobrine', $mcPlayer->uuid);

        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.create', $ban));
    }

    public function test_errors_if_mojang_rate_limits()
    {
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')
                ->andThrow(new TooManyRequestsException('rate_limited', 'Too many requests sent to the Mojang API'));
        });

        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_no_active_bans()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()->inactive()->for($mcPlayer, 'bannedPlayer')->create();
        $this->mockMojangToReturn('Herobrine', $mcPlayer->uuid);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_no_bans()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->mockMojangToReturn('Herobrine', $mcPlayer->uuid);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_not_valid_username()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUuidOf')
                ->with('Herobrine')
                ->once()->andReturn(null);
        });
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_player_not_known()
    {
        $this->mockMojangToReturn('Herobrine', $this->faker->uuid);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }
}
