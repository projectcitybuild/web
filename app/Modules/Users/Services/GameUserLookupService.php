<?php
namespace App\Modules\Users\Services;

use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;

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
     * Gets the GameUser id that belongs to the given alias type and alias, or
     * alternatively creates a new GameUser if one does not exist
     *
     * @param int $aliasType
     * @param string $alias
     * @param array $extraAliases   Array of extra aliases to create for the user if new (key = type, value = alias)
     * @return GameUser
     */
    public function getOrCreateGameUserId(int $aliasType, string $alias, array $extraAliases = []) : int {
        $playerAlias = $this->aliasRepository->getAlias($aliasType, $alias);
        if(is_null($playerAlias)) {
            $player = $this->gameUserRepository->store();
            $playerId = $player->game_user_id;

            $this->aliasRepository->store(
                $aliasType,
                $playerId, 
                $alias
            );

            if(count($extraAliases) > 0) {
                foreach($extraAliases as $type => $extraAlias) {
                    $extraAliasTypeId = $this->aliasRepository->getAliasType($type);
                    $this->aliasRepository->store(
                        $type,
                        $playerId,
                        $extraAlias
                    );
                }
            }

            return $playerId;
        }

        return $playerAlias->game_user_id;
    }

}