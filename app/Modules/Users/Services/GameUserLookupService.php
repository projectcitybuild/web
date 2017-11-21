<?php
namespace App\Modules\Users\Services;

use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Services\GameUserCreationService;
use App\Modules\Users\Models\GameUser;
use Illuminate\Database\Connection;

class GameUserLookupService {

    /**
     * @var UserAliasRepository
     */
    private $aliasRepository;

    /**
     * @var GameUserCreationService
     */
    private $gameUserCreationService;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        UserAliasRepository $aliasRepository,
        GameUserCreationService $gameUserCreationService,
        Connection $connection
    ) {
        $this->aliasRepository = $aliasRepository;
        $this->gameUserCreationService = $gameUserCreationService;
        $this->connection = $connection;
    }

    /**
     * Gets the GameUser that belongs to the given alias type and alias
     *
     * @param int $aliasType
     * @param string $alias
     * @return GameUser|null
     */
    public function getGameUser(int $aliasType, string $alias) : ?GameUser {
        $playerAlias = $this->aliasRepository->getAlias($aliasType, $alias);
        if($playerAlias !== null) {
            return $playerAlias->gameUser;
        }
        return null;
    }

    /**
     * Gets the GameUser that belongs to the given alias type and alias, or
     * alternatively creates a new GameUser if one does not exist
     *
     * @param int $aliasType        Alias type to search for [UserAliasTypeEnum]
     * @param string $alias         Alias to search for
     * @return GameUser
     */
    public function getOrCreateGameUser(int $aliasType, string $alias) : GameUser {
        $gameUser = $this->getGameUser($aliasType, $alias);
        if($gameUser !== null) {
            return $gameUser;
        }

        return $this->gameUserCreationService->createUser([$aliasType => $alias]);
    }

}