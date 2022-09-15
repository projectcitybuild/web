<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use Domain\Warnings\UseCases\AcknowledgeWarning;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\PlayerWarning;
use Repositories\Warnings\MockPlayerWarningRepository;
use Repositories\Warnings\PlayerWarningRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class AcknowledgeWarningTest extends TestCase
{
    private PlayerWarningRepository $playerWarningRepository;
    private AcknowledgeWarning $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerWarningRepository = new MockPlayerWarningRepository();

        $this->useCase = new AcknowledgeWarning(
            playerWarningRepository: $this->playerWarningRepository,
        );
    }

    public function test_throws_404_if_warning_not_found()
    {
        $this->playerWarningRepository->find = null;
        $this->expectException(NotFoundHttpException::class);
        $this->useCase->execute(warningId: 1);
    }

    public function test_throws_403_if_warned_player_is_different()
    {
        $warning = PlayerWarning::factory()
            ->id()
            ->warnedPlayer(
                MinecraftPlayer::factory()->id()->for(Account::factory(['account_id' => 1]))
            )
            ->warnedBy(MinecraftPlayer::factory())
            ->make();

        $this->playerWarningRepository->find = $warning;

        try {
            $this->useCase->execute(
                warningId: $warning->getKey(),
                accountId: 2,
            );
        } catch (HttpException $e) {
        }

        $this->assertEquals(
            expected: new HttpException(403),
            actual: $e,
        );
    }

    public function test_throws_410_if_warning_already_acknowledged()
    {
        $warning = PlayerWarning::factory()
            ->id()
            ->withPlayers()
            ->acknowledged()
            ->make();

        $this->playerWarningRepository->find = $warning;

        try {
            $this->useCase->execute(warningId: $warning->getKey());
        } catch (HttpException $e) {
        }

        $this->assertEquals(
            expected: new HttpException(410),
            actual: $e,
        );
    }

    public function test_acknowledges_warning()
    {
        $this->setTestNow();

        $warning = PlayerWarning::factory()
            ->id()
            ->acknowledged(false)
            ->withPlayers()
            ->make();

        $this->playerWarningRepository->find = $warning;

        $this->useCase->execute(warningId: $warning->getKey());

        $this->assertTrue($warning->is_acknowledged);
        $this->assertEquals($warning->acknowledged_at, now());
    }
}
