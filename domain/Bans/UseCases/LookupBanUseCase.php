<?php

namespace Domain\Bans\UseCases;

use App\Exceptions\Http\TooManyRequestsException;
use Domain\Bans\Exceptions\NotBannedException;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\MinecraftUUID;
use Library\Mojang\Api\MojangPlayerApi;
use Repositories\GameBanRepository;
use Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

class LookupBanUseCase
{
    public function __construct(
        private readonly MojangPlayerApi $mojangPlayerApi,
        private readonly GameBanRepository $gameBanRepository,
        private readonly MinecraftPlayerRepository $minecraftPlayerRepository
    ) {
    }

    /**
     * @throws TooManyRequestsException
     * @throws NotBannedException
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
            throw new NotBannedException();
        }

        $gameBan = $this->gameBanRepository->firstActiveBan(player: $mcPlayer);

        if ($gameBan === null) {
            throw new NotBannedException();
        }

        return $gameBan;
    }
}
