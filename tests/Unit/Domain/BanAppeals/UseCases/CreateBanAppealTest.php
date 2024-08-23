<?php

namespace Tests\Unit\Domain\BanAppeals\UseCases;

use App\Domains\BanAppeals\Exceptions\EmailRequiredException;
use App\Domains\BanAppeals\Notifications\BanAppealConfirmationNotification;
use App\Domains\BanAppeals\UseCases\CreateBanAppeal;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Notification;
use Repositories\BanAppealRepository;
use Tests\TestCase;

class CreateBanAppealTest extends TestCase
{
    private BanAppealRepository $banAppealRepository;
    private CreateBanAppeal $useCase;
    private GamePlayerBan $gamePlayerBan;
    private BanAppeal $banAppeal;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $this->banAppealRepository = \Mockery::mock(BanAppealRepository::class);
        $this->useCase = new CreateBanAppeal(
            banAppealRepository: $this->banAppealRepository
        );
        $this->gamePlayerBan = GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();

        $this->banAppeal = \Mockery::mock(BanAppeal::class);
        $this->banAppeal->makePartial()
            ->allows([
                'showLink' => 'https://example.org',
            ]);
    }

    public function test_creates_appeal()
    {
        $this->banAppealRepository
            ->shouldReceive('create')
            ->with($this->gamePlayerBan->getKey(), false, 'Explanation', 'test@example.org')
            ->andReturn($this->banAppeal);

        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: null, email: 'test@example.org');

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_requires_email_with_no_logged_in_account()
    {
        $this->expectException(EmailRequiredException::class);
        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: null, email: null);
    }

    public function test_requires_email_if_accounts_not_matching()
    {
        $this->expectException(EmailRequiredException::class);
        $this->gamePlayerBan->bannedPlayer->account()->associate(Account::factory()->create());
        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: Account::factory()->create(), email: null);
    }

    public function test_email_not_required_if_account_matches()
    {
        $account = Account::factory()->create();
        $this->gamePlayerBan->bannedPlayer->account()->associate($account);
        $this->banAppealRepository
            ->shouldReceive('create')
            ->with($this->gamePlayerBan->getKey(), true, 'Explanation', null)
            ->andReturn($this->banAppeal);

        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: $account, email: null);

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_sends_email_to_to_appeal_notifiable()
    {
        Notification::assertNothingSent();
        $this->banAppealRepository
            ->allows('create')
            ->withAnyArgs()
            ->andReturn($this->banAppeal);
        $banAppeal = $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: null, email: 'test@example.org');
        Notification::assertSentTo([$banAppeal], BanAppealConfirmationNotification::class);
    }

    public function test_sets_verified_if_account_matches_player_owner()
    {
        $account = Account::factory()->create();
        $this->gamePlayerBan->bannedPlayer->account()->associate($account);
        $this->banAppealRepository
            ->shouldReceive('create')
            ->with($this->gamePlayerBan->getKey(), true, 'Explanation', null)
            ->andReturn($this->banAppeal);
        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: $account, email: null);

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_sets_not_verified_if_no_account_specified()
    {
        $this->gamePlayerBan->bannedPlayer->account()->associate(Account::factory()->create());
        $this->banAppealRepository
            ->shouldReceive('create')
            ->with($this->gamePlayerBan->getKey(), false, 'Explanation', 'test@example.org')
            ->andReturn($this->banAppeal);
        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: null, email: 'test@example.org');

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_sets_not_verified_if_different_account_used()
    {
        $this->gamePlayerBan->bannedPlayer->account()->associate(Account::factory()->create());
        $this->banAppealRepository
            ->shouldReceive('create')
            ->with($this->gamePlayerBan->getKey(), false, 'Explanation', 'test@example.org')
            ->andReturn($this->banAppeal);
        $this->useCase->execute($this->gamePlayerBan, 'Explanation', loggedInAccount: Account::factory()->create(), email: 'test@example.org');

        // Skip risky warning
        $this->assertTrue(true);
    }
}
