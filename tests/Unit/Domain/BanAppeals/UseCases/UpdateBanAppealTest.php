<?php

namespace Tests\Unit\Domain\BanAppeals\UseCases;

use App\Core\Exceptions\NotImplementedException;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use Domain\BanAppeals\UseCases\UpdateBanAppeal;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UnbanType;
use Domain\Bans\UseCases\CreatePlayerUnban;
use Domain\Panel\Exceptions\NoPlayerForActionException;
use Repositories\BanAppealRepository;
use Tests\TestCase;

class UpdateBanAppealTest extends TestCase
{
    private UpdateBanAppeal $useCase;
    private BanAppealRepository $banAppealRepository;
    private CreatePlayerUnban $unbanUseCase;
    private Account $decidingAccount;
    private MinecraftPlayer $decidingPlayer;
    private MinecraftPlayer $bannedPlayer;
    private GamePlayerBan $gamePlayerBan;
    private BanAppeal $banAppeal;
    private string $decisionNote = 'Some decision note';

    protected function setUp(): void
    {
        parent::setUp();

        $this->banAppealRepository = \Mockery::mock(BanAppealRepository::class);
        $this->unbanUseCase = \Mockery::mock(CreatePlayerUnban::class);
        $this->useCase = new UpdateBanAppeal(
            banAppealRepository: $this->banAppealRepository,
            unbanUseCase: $this->unbanUseCase
        );

        $this->decidingAccount = Account::factory()->create();
        $this->decidingPlayer = MinecraftPlayer::factory()->for($this->decidingAccount)->create();
        $this->bannedPlayer = MinecraftPlayer::factory()->create();
        $this->gamePlayerBan = GamePlayerBan::factory()->bannedPlayer($this->bannedPlayer)->create();
        $this->banAppeal = BanAppeal::factory()->for($this->gamePlayerBan)->create();
    }

    public function test_can_unban()
    {
        $this->banAppealRepository->expects('updateDecision')
            ->once()
            ->with($this->banAppeal, $this->decisionNote, $this->decidingPlayer->getKey(), BanAppealStatus::ACCEPTED_UNBAN);

        $this->unbanUseCase->expects('execute')
            ->once()
            ->with(
                \Mockery::on(function ($arg) {
                    return $arg->key == $this->bannedPlayer->getkey();
                }),
                \Mockery::on(function ($arg) {
                    return $arg->key == $this->decidingPlayer->getKey();
                }),
                UnbanType::APPEALED,
            )->andReturn($this->gamePlayerBan);

        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingAccount: $this->decidingAccount,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_UNBAN
        );
    }

    public function test_can_deny()
    {
        $this->banAppealRepository->expects('updateDecision')
            ->once()
            ->with($this->banAppeal, $this->decisionNote, $this->decidingPlayer->getKey(), BanAppealStatus::DENIED);

        $this->unbanUseCase->expects('execute')->never();

        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingAccount: $this->decidingAccount,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::DENIED
        );
    }

    /**
     * TODO: implement once tempbans are sorted
     */
    public function test_rejects_tempban()
    {
        $this->expectException(NotImplementedException::class);
        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingAccount: $this->decidingAccount,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_TEMPBAN
        );
    }

    public function test_throws_exception_if_appeal_decided()
    {
        $decidedAppeal = BanAppeal::factory()->unbanned()->for($this->gamePlayerBan)->create();
        $this->expectException(AppealAlreadyDecidedException::class);
        $this->useCase->execute(
            banAppeal: $decidedAppeal,
            decidingAccount: $this->decidingAccount,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_UNBAN,
        );
    }

    public function test_throws_exception_if_no_player_for_action()
    {
        $accountWithNoPlayer = Account::factory()->create();
        $this->expectException(NoPlayerForActionException::class);
        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingAccount: $accountWithNoPlayer,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_UNBAN,
        );
    }

    public function test_throws_exception_if_unbanned_manually()
    {
        $this->banAppealRepository->expects('updateDecision')
            ->once();

        $this->unbanUseCase->expects('execute')
            ->once()
            ->andThrows(NotBannedException::class);

        $this->expectException(NotBannedException::class);
        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingAccount: $this->decidingAccount,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_UNBAN,
        );
    }
}
