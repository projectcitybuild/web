<?php
namespace App\Modules\Users\Services;

use Illuminate\Database\Connection;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Models\GameUser;

class GameUserCreationService {

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var GameUserRepository
     */
    private $gameUserRepository;

    /**
     * @var UserAliasRepository
     */
    private $aliasRepository;

    public function __construct(
        Connection $connection,
        GameUserRepository $gameUserRepository,
        UserAliasRepository $aliasRepository
    ) {
        $this->connection = $connection;
        $this->gameUserRepository = $gameUserRepository;
        $this->aliasRepository = $aliasRepository;
    }

    /**
     * Creates a new game user and their associated user aliases
     *
     * @param array $aliases
     * @return GameUser
     */
    public function createUser(array $aliases) : GameUser {
        if(count($aliases) == 0) {
            throw new \Exception('An array of aliases is required when creating a game user');
        }

        $this->connection->beginTransaction();
        try {
            $player = $this->gameUserRepository->store();
            foreach($aliases as $aliasType => $alias) {
                $this->aliasRepository->store($aliasType, $alias, $player->game_user_id);
            }

            $this->connection->commit();
            return $player;
        
        } catch(Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

}