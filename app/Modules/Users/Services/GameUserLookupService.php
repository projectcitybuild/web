<?php
namespace App\Modules\Users\Services;

use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Models\GameUser;

class GameUserLookupService {

    /**
     * @var GameUserRepository
     */
    private $gameUserRepository;

    /**
     * @var UserAliasRepository
     */
    private $aliasRepository;

    public function __construct(GameUserRepository $gameUserRepository, UserAliasRepository $aliasRepository) {
        $this->gameUserRepository = $gameUserRepository;
        $this->aliasRepository = $aliasRepository;
    }

    /**
     * Gets the GameUser that belongs to the given alias type and alias, or
     * alternatively creates a new GameUser if one does not exist
     *
     * @param int $aliasType        Alias type to search for [UserAliasTypeEnum]
     * @param string $alias         Alias to search for
     * @param array $extraAliases   Array of extra aliases to create for the user if new (key = type, value = alias)
     * @return GameUser
     */
    public function getOrCreateGameUser(int $aliasType, string $alias, array $extraAliases = []) : GameUser {
        $playerAlias = $this->aliasRepository->getAlias($aliasType, $alias);
        if(isset($playerAlias)) {
            return $playerAlias->gameUser;
        }

        $player = $this->gameUserRepository->store();
        $playerId = $player->game_user_id;

        $this->aliasRepository->store(
            $aliasType,
            $playerId, 
            $alias
        );

        foreach($extraAliases as $type => $extraAlias) {
            $this->aliasRepository->store($type, $playerId, $extraAlias);
        }

        return $player;
    }

}