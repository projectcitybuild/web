<?php

namespace App\Domains\Bans\UseCases;

use Repositories\GamePlayerBanRepository;

final class ExpirePlayerBans
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
