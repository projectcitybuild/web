<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Exceptions\NotIPBannedException;
use App\Models\GameIPBan;

class CreateIPUnban
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    public function execute(
        string $ip,
        PlayerIdentifier $unbannerPlayerIdentifier,
        UnbanType $unbanType,
    ): GameIPBan {
        $existingBan = GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first()
            ?? throw new NotIPBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
