<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Data\MinecraftUUID;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Core\Domains\PlayerLookup\Exceptions\PlayerNotFoundException;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Models\GamePlayerBan;
use Repositories\GamePlayerBanRepository;
use Repositories\MinecraftPlayerRepository;

class LookupPlayerBan
{
    public function __construct(
        private readonly MojangPlayerApi $mojangPlayerApi,
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
        private readonly MinecraftPlayerRepository $minecraftPlayerRepository
    ) {
    }

    /**
     * @throws TooManyRequestsException
     * @throws NotBannedException
     * @throws PlayerNotFoundException
     */
    public function execute(string $username): GamePlayerBan
    {
        $mojangPlayer = $this->mojangPlayerApi->getUuidOf($username);

        if ($mojangPlayer === null) {
            throw new PlayerNotFoundException();
        }

        $mcPlayer = $this->minecraftPlayerRepository->getByUUID(new MinecraftUUID($mojangPlayer->getUuid()));
        if ($mcPlayer === null) {
            throw new NotBannedException();
        }

        $gamePlayerBan = $this->gamePlayerBanRepository->firstActiveBan(player: $mcPlayer);

        if ($gamePlayerBan === null) {
            throw new NotBannedException();
        }

        return $gamePlayerBan;
    }
}
