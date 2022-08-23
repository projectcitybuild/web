<?php

namespace Domain\Bans\UseCases;

use Repositories\GameBanRepository;

final class ExpireBansUseCase
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
    ) {
    }

    public function execute() {
        $this->gameBanRepository->deactivateActiveExpired();
    }
}
