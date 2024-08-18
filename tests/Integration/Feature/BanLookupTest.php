<?php

namespace Tests\Integration\Feature;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UseCases\LookupPlayerBan;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Tests\TestCase;

class BanLookupTest extends TestCase
{
    use WithFaker;

    private function mockUseCaseToReturnBan(string $username, GamePlayerBan $ban)
    {
        $this->mock(LookupPlayerBan::class, function (MockInterface $mock) use ($username, $ban) {
            $mock->shouldReceive('execute')
                ->with($username)
                ->andReturn($ban);
        });
    }

    private function mockUseCaseToThrow(string $exception)
    {
        $this->mock(LookupPlayerBan::class, function (MockInterface $mock) use ($exception) {
            $mock->shouldReceive('execute')
                ->andThrow($exception);
        });
    }

    public function test_can_submit_lookup_as_guest()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GamePlayerBan::factory()->for($mcPlayer, 'bannedPlayer')->create();

        $this->mockUseCaseToReturnBan('Herobrine', $ban);

        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.create', $ban));
    }

    public function test_can_submit_lookup_as_user()
    {
        $this->actingAs(Account::factory()->create());
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GamePlayerBan::factory()->for($mcPlayer, 'bannedPlayer')->create();

        $this->mockUseCaseToReturnBan('Herobrine', $ban);

        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.create', $ban));
    }

    public function test_errors_if_throws_rate_limit()
    {
        $this->mockUseCaseToThrow(TooManyRequestsException::class);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_no_active_bans()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $ban = GamePlayerBan::factory()->inactive()->for($mcPlayer, 'bannedPlayer')->create();
        $this->mockUseCaseToThrow(NotBannedException::class);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_no_bans()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->mockUseCaseToThrow(NotBannedException::class);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_not_valid_username()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->mockUseCaseToThrow(PlayerNotFoundException::class);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }

    public function test_errors_if_player_not_known()
    {
        $this->mockUseCaseToThrow(PlayerNotFoundException::class);
        $this->post(route('front.bans.lookup'), ['username' => 'Herobrine'])
            ->assertSessionHasErrors();
    }
}
