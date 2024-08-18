<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Entities\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Domains\Bans\Exceptions\NotIPBannedException;
use App\Domains\Bans\UnbanType;
use App\Models\GameIPBan;
use Repositories\GameIPBans\GameIPBanRepository;

class CreateIPUnban
{
    public function __construct(
        private readonly GameIPBanRepository $gameIPBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    public function execute(
        string $ip,
        PlayerIdentifier $unbannerPlayerIdentifier,
        UnbanType $unbanType,
    ): GameIPBan {
        $existingBan = $this->gameIPBanRepository->firstActive(ip: $ip)
            ?? throw new NotIPBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        $this->gameIPBanRepository->unban(
            ban: $existingBan,
            unbannerPlayerId: $unbannerPlayer->getKey(),
            unbanType: $unbanType,
        );

        return $existingBan->refresh();
    }
}
