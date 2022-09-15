<?php

namespace Domain\Bans\UseCases;

use Repositories\GameBanRepository;

final class ExpireBans
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
    ) {
    }

    public function execute()
    {
        $this->gameBanRepository->unbanAllExpired();
    }
}
