<?php
namespace App\Modules\Users\Services;

use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Models\UserAliasType;

class GameUserLookupService {

    private $aliasTypeCache = [];

    private $gameUserRepository;
    private $aliasRepository;

    public function __construct(GameUserRepository $gameUserRepository, UserAliasRepository $aliasRepository) {
        $this->gameUserRepository = $gameUserRepository;
        $this->aliasRepository = $aliasRepository;
    }

    /**
     * Validates that the given identifier type matches a type stored in our database
     *
     * @param string $identifierType
     * @throws InvalidAliastypeException
     * @return int
     */
    private function getAliasTypeId(string $identifierType) : int {
        // TODO: use persistent storage
        if(array_key_exists($identifierType, $this->aliasTypeCache)) {
            return $this->aliasTypeCache[$identifierType];
        }

        $aliasType = $this->aliasRepository->getAliasType($identifierType);
        if(is_null($aliasType)) {
            throw new InvalidAliasTypeException('Invalid identifier type given ['.$identifierType.']');
        }

        $aliasTypeId = $aliasType->user_alias_type_id;
        $this->aliasTypeCache[$identifierType] = $aliasTypeId;
        return $aliasTypeId;
    }

    /**
     * Gets the GameUser id that belongs to the given alias type and alias, or
     * alternatively creates a new GameUser if one does not exist
     *
     * @param string $aliasType
     * @param string $alias
     * @param array $extraAliases   Array of extra aliases to create for the user if new (key = type, value = alias)
     * @return GameUser
     */
    public function getOrCreateGameUserId(string $aliasType, string $alias, array $extraAliases = []) : int {
        $aliasTypeId = $this->getAliasTypeId($aliasType);
        $playerAlias = $this->aliasRepository->getAlias($aliasTypeId, $alias);
        if(is_null($playerAlias)) {
            $player = $this->gameUserRepository->store();
            $playerId = $player->game_user_id;

            $this->aliasRepository->store(
                $aliasTypeId,
                $playerId, 
                $alias
            );

            if(count($extraAliases) > 0) {
                foreach($extraAliases as $type => $extraAlias) {
                    $extraAliasTypeId = $this->aliasRepository->getAliasType($type);
                    $this->aliasRepository->store(
                        $this->aliasRepository->getAliasType($type),
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