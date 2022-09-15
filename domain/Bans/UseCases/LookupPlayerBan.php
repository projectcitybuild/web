<?php

namespace Domain\Bans\UseCases;

use App\Exceptions\Http\TooManyRequestsException;
use Domain\Bans\Exceptions\NotBannedException;
use Entities\Models\Eloquent\GamePlayerBan;
use Entities\Models\MinecraftUUID;
use Library\Mojang\Api\MojangPlayerApi;
use Repositories\GamePlayerBanRepository;
use Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

class LookupPlayerBan
{
    public function __construct(
        private readonly MojangPlayerApi           $mojangPlayerApi,
        private readonly GamePlayerBanRepository   $gamePlayerBanRepository,
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
