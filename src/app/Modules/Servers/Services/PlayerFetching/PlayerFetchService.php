<?php
namespace App\Modules\Servers\Services\PlayerFetching;

use App\Modules\Servers\Repositories\ServerStatusPlayerRepository;


class PlayerFetchService {

    /**
     * @var PlayerFetchAdapterInterface
     */
    private $adapter;

    /**
     * @var ServerStatusPlayerRepository
     */
    private $statusPlayerRepository;


    public function __construct(ServerStatusPlayerRepository $statusPlayerRepository) {
        $this->statusPlayerRepository = $statusPlayerRepository;
    }

    public function setAdapter(PlayerFetchAdapterInterface $adapter) : PlayerFetchService {
        $this->adapter = $adapter;
        return $this;
    }

    public function associatePlayersToServerStatus(int $serverStatusId, array $aliases) {
        if(count($aliases) === 0) {
            return;
        }

        $identifiers = $this->adapter->getUniqueIdentifiers($aliases);
        $players = $this->adapter->createPlayers($identifiers);

        foreach($players as $player) {
            $this->statusPlayerRepository->store($serverStatusId, $player->getKey(), get_class($player));
        }
    }

}