<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\GamePlayerBan;
use Illuminate\Support\Collection;
use Repositories\GamePlayerBanRepository;

final class GetAllPlayerBans
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return Collection
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): Collection {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return collect();
        }
        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();
    }
}
