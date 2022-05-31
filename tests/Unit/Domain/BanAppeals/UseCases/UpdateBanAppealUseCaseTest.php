<?php

namespace Tests\Domain\BanAppeals\UseCases;

use App\Exceptions\Http\NotImplementedException;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Repositories\BanAppealRepository;
use Domain\BanAppeals\UseCases\UpdateBanAppealUseCase;
use Domain\Bans\UseCases\CreateUnbanUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\GameUnban;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Support\Facades\Notification;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Tests\TestCase;

class UpdateBanAppealUseCaseTest extends TestCase
{
    private UpdateBanAppealUseCase $useCase;
    private BanAppealRepository $banAppealRepository;
    private CreateUnbanUseCase $unbanUseCase;

    private MinecraftPlayer $banningPlayer;
    private MinecraftPlayer $decidingPlayer;
    private MinecraftPlayer $bannedPlayer;
    private GameBan $gameBan;
    private BanAppeal $banAppeal;

    private string $decisionNote = 'Some decision note';

    protected function setUp(): void
    {
        parent::setUp();

        $this->banAppealRepository = \Mockery::mock(BanAppealRepository::class);
        $this->unbanUseCase = \Mockery::mock(CreateUnbanUseCase::class);
        $this->useCase = new UpdateBanAppealUseCase(
            banAppealRepository: $this->banAppealRepository,
            unbanUseCase: $this->unbanUseCase
        );

        $this->banningPlayer = MinecraftPlayer::factory()->for(Account::factory())->create();
        $this->decidingPlayer = MinecraftPlayer::factory()->for(Account::factory())->create();
        $this->bannedPlayer = MinecraftPlayer::factory()->create();
        $this->gameBan = GameBan::factory()->bannedPlayer($this->bannedPlayer)->active()->create();
        $this->banAppeal = BanAppeal::factory()->for($this->gameBan)->create();
    }

    private function createUnban()
    {
        return GameUnban::factory()->staffPlayer($this->decidingPlayer)->for($this->gameBan, 'ban')->create();
    }

    public function test_can_unban()
    {
        $this->banAppealRepository->expects('updateDecision')
            ->once()
            ->with($this->banAppeal, $this->decisionNote, BanAppealStatus::ACCEPTED_UNBAN);

        $this->unbanUseCase->expects('execute')
            ->once()
            ->with(
                \Mockery::on(function ($arg) {
                    return $arg->key == $this->bannedPlayer->getkey();
                }),
                \Mockery::on(function ($arg) {
                    return $arg->key == $this->decidingPlayer->uuid;
                })
            )->andReturn($this->createUnban());

        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingPlayer: $this->decidingPlayer,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_UNBAN
        );
    }

    public function test_can_deny()
    {
        $this->banAppealRepository->expects('updateDecision')
            ->once()
            ->with($this->banAppeal, $this->decisionNote, BanAppealStatus::DENIED);

        $this->unbanUseCase->expects('execute')->never();

        $this->useCase->execute(
            banAppeal: $this->banAppeal,
            decidingPlayer: $this->decidingPlayer,
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
            decidingPlayer: $this->decidingPlayer,
            decisionNote: $this->decisionNote,
            status: BanAppealStatus::ACCEPTED_TEMPBAN
        );
    }
}
