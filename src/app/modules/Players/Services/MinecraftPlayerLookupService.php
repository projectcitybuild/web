<?php
namespace App\Modules\Players\Services;

use App\Modules\Players\Repositories\MinecraftPlayerRepository;
use App\Modules\Players\Repositories\MinecraftPlayerAliasRepository;
use App\Modules\Players\Models\MinecraftPlayer;
use Illuminate\Database\Connection;

class MinecraftPlayerLookupService
{

    /**
     * @var MinecraftPlayerRepository
     */
    private $playerRepository;
    
    /**
     * @var MinecraftPlayerAliasRepository
     */
    private $aliasRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(
        MinecraftPlayerRepository $playerRepository,
        MinecraftPlayerAliasRepository $aliasRepository,
        Connection $connection
    ) {
        $this->playerRepository = $playerRepository;
        $this->aliasRepository = $aliasRepository;
        $this->connection = $connection;
    }


    public function getByUuid(string $uuid) : ?MinecraftPlayer
    {
        return $this->playerRepository->getByUuid($uuid);
    }

    public function getByAlias(string $alias) : ?MinecraftPlayer
    {
        $alias = $this->aliasRepository->getByAlias($alias);
        if ($alias === null) {
            return null;
        }

        return $alias->player;
    }

    /**
     * Gets a MinecraftPlayer by uuid. If the uuid doesn't match
     * a player, the player is created first
     *
     * @param string $uuid
     * @param string|null $createAlias
     * @return MinecraftPlayer
     */
    public function getOrCreateByUuid(string $uuid, ?string $createAlias = null) : MinecraftPlayer
    {
        $player = $this->getByUuid($uuid);
        if ($player !== null) {
            return $player;
        }
        
        $this->connection->beginTransaction();
        try {
            $player = $this->playerRepository->store($uuid);

            if (!empty($createAlias)) {
                $this->aliasRepository->store($player->player_minecraft_id, $createAlias);
            }

            $this->connection->commit();
            return $player;
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
