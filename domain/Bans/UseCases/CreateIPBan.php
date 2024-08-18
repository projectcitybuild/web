<?php

namespace Domain\Bans\UseCases;

use App\Models\GameIPBan;
use Domain\Bans\Exceptions\AlreadyIPBannedException;
use Repositories\GameIPBans\GameIPBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\PlayerLookup;

final class CreateIPBan
{
    public function __construct(
        private readonly GameIPBanRepository $gameIPBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    public function execute(
        string $ip,
        PlayerIdentifier $bannerPlayerIdentifier,
        string $bannerPlayerAlias,
        string $banReason,
    ): GameIPBan {
        $existingBan = $this->gameIPBanRepository->firstActive(ip: $ip);
        if ($existingBan !== null) {
            throw new AlreadyIPBannedException();
        }

        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        return $this->gameIPBanRepository->create(
            ip: $ip,
            bannerPlayerId: $bannerPlayer->getKey(),
            reason: $banReason,
        );
    }
}
