<?php

namespace Domain\Bans\UseCases;

use App\Exceptions\Http\TooManyRequestsException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\Repositories\GameBanRepository;
use Entities\MinecraftUUID;
use Entities\Models\Eloquent\GameBan;
use Library\Mojang\Api\MojangPlayerApi;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\Repositories\MinecraftPlayerRepository;

class LookupBanUseCase
{
    public function __construct(
        private MojangPlayerApi $mojangPlayerApi,
        private GameBanRepository $gameBanRepository,
        private MinecraftPlayerRepository $minecraftPlayerRepository
    ) {}

    /**
     * @throws TooManyRequestsException
     * @throws PlayerNotBannedException
     * @throws PlayerNotFoundException
     */
    public function execute(string $username): GameBan
    {
        $player = $this->mojangPlayerApi->getUuidOf($username);

        if ($player === null) {
            throw new PlayerNotFoundException();
        }

        $mcPlayer = $this->minecraftPlayerRepository->getByUUID(new MinecraftUUID($player->getUuid()));
        if ($mcPlayer === null) {
            throw new PlayerNotBannedException();
        }

        $playerIdentifier = PlayerIdentifier::minecraftUUID($mcPlayer->getKey());
        $gameBan = $this->gameBanRepository->firstActiveBan($playerIdentifier);

        if ($gameBan === null) {
            throw new PlayerNotBannedException();
        }

        return $gameBan;
    }
}
