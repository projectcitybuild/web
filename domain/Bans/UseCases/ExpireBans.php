<?php

namespace Domain\Bans\UseCases;

use Repositories\GamePlayerBanRepository;

final class ExpireBans
{
    public function __construct(
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
    ) {
    }

    public function execute()
    {
        $this->gamePlayerBanRepository->unbanAllExpired();
    }
}
