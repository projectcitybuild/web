<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Domains\Bans\Exceptions\AlreadyIPBannedException;
use App\Models\GameIPBan;

final class CreateIPBan
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    public function execute(
        string $ip,
        PlayerIdentifier $bannerPlayerIdentifier,
        string $bannerPlayerAlias,
        string $banReason,
    ): GameIPBan {
        $existingBan = GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first();

        if ($existingBan !== null) {
            throw new AlreadyIPBannedException();
        }

        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        return GameIPBan::create([
            'banner_player_id' => $bannerPlayer->getKey(),
            'ip_address' => $ip,
            'reason' => $banReason,
        ]);
    }
}
