<?php

namespace Domain\Bans\UseCases;

use App\Exceptions\Http\TooManyRequestsException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\MinecraftUUID;
use Library\Mojang\Api\MojangPlayerApi;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\Repositories\MinecraftPlayerRepository;

class LookupBanUseCase
{
    public function __construct(
        private MojangPlayerApi $mojangPlayerApi,
        private GameBanRepository $gameBanRepository,
        private MinecraftPlayerRepository $minecraftPlayerRepository
    ) {
    }

    /**
     * @throws TooManyRequestsException
     * @throws PlayerNotBannedException
     * @throws PlayerNotFoundException
     */
    public function execute(string $username): GameBan
    {
        $mojangPlayer = $this->mojangPlayerApi->getUuidOf($username);

        if ($mojangPlayer === null) {
            throw new PlayerNotFoundException();
        }

        $mcPlayer = $this->minecraftPlayerRepository->getByUUID(new MinecraftUUID($mojangPlayer->getUuid()));
        if ($mcPlayer === null) {
            throw new PlayerNotBannedException();
        }

        $gameBan = $this->gameBanRepository->firstActiveBan(player: $mcPlayer);

        if ($gameBan === null) {
            throw new PlayerNotBannedException();
        }

        return $gameBan;
    }
}
