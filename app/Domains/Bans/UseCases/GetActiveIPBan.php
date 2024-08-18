<?php

namespace App\Domains\Bans\UseCases;

use App\Models\GameIPBan;
use Repositories\GameIPBans\GameIPBanRepository;

final class GetActiveIPBan
{
    public function __construct(
        private readonly GameIPBanRepository $gameIPBanRepository,
    ) {
    }

    public function execute(string $ip): ?GameIPBan
    {
        return $this->gameIPBanRepository->firstActive(ip: $ip);
    }
}
