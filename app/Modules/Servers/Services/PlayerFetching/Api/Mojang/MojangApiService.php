<?php
namespace App\Modules\Servers\Services\PlayerFetching\Api\Mojang;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangPlayer;
use App\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangPlayerNameHistory;

class MojangApiService {

    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Retrieves the UUID that belongs to the given name at the given time.
     * The name returned is the current name of the player who owned the name at the time.
     *
     * If no time given, uses the current time.
     *
     * @param $name
     * @param null $time
     * @return MojangPlayer
     */
    public function getUuidOf(string $name, $time = null) : ?MojangPlayer {
        if(is_null($time)) {
            $time = time();
        }
        
        $response = $this->client->request('GET', 'https://api.mojang.com/users/profiles/minecraft/' . $name, [
            'query' => [
                'at' => $time,
            ],
        ]);

        // if no player exists, return null
        if($response->getStatusCode() === 204) {
            return null;
        }

        if($response->getStatusCode() !== 200) {
            throw new \Exception('Bad Mojang api response: ' . $response->getStatusCode() . ' error');
        }

        $body = json_decode($response->getBody());
        return new MojangPlayer(
            $body->id, 
            $body->name, 
            isset($body->legacy),
            isset($body->demo)
        );
    }

    /**
     * Retrieves the UUID for the person who first registered
     * the given name, regardless of who currently owns it now.
     *
     * @param $name
     * @return MojangPlayer
     */
    public function getOriginalOwnerUuidOf(string $name) : MojangPlayer {
        return $this->getUuidOf($name, 0);
    }

    /**
     * Retrieves UUIDs for every name in the given array, in a
     * single lookup.
     *
     * The API only allows a max of 100 names per lookup.
     *
     * @param array $names
     * @return array
     * @throws \Exception
     */
    public function getUuidBatchOf(array $names) : ?array {
        if(count($names) === 0 || count($names) > 100) {
            throw new \Exception('Batch must contain between 1 and 100 names to search');
        }

        // check for invalid names before hitting the api, or else
        // the entire request could fail midway
        foreach($names as $name) {
            if(empty($name)) {
                throw new \Exception('Name cannot be null or empty');
            }
        }

        $response = $this->client->request('POST', 'https://api.mojang.com/profiles/minecraft', [
            'json' => $names,
        ]);

        if($response->getStatusCode() !== 200) {
            throw new \Exception('Bad Mojang api response: ' . $response->getStatusCode() . ' error');
        }

        $data = json_decode($response->getBody());
        if(count($data) === 0) {
            return [];
        }

        return collect($data)
            ->keyBy(function($player) {
                return $player->name;
            })
            ->map(function($player) {
                return new MojangPlayer(
                    $player->id,
                    $player->name,
                    isset($body->legacy),
                    isset($body->demo)
                );
            })
            ->toArray();
    }

    /**
     * Returns all the usernames this user has used in the past and
     * the one they are using currently.
     *
     * The UUID must be given without hyphens.
     *
     * @param $uuid
     * @return array|null
     */
    public function getNameHistoryOf($uuid) : ?MojangPlayerNameHistory {
        $response = $this->client->request('GET', 'https://api.mojang.com/user/profiles/' . $uuid . '/names');

        // if no player exists, return null
        if($response->getStatusCode() === 204) {
            return null;
        }
        
        if($response->getStatusCode() !== 200) {
            throw new \Exception('Bad Mojang api response: ' . $response->getStatusCode() . ' error');
        }

        return new MojangPlayerNameHistory(
            json_decode($response->getBody())
        );
    }

    /**
     * Returns all the usernames this user has used in the past and
     * the one they are using currently.
     *
     * Performs two lookups as the original API can only be queried using an UUID.
     *
     * @param $name
     * @return array|null
     */
    public function getNameHistoryByNameOf($name) : ?MojangPlayerNameHistory {
        $player = $this->getUuidOf($name);
        if($player !== null) {
            return $this->getNameHistoryOf($player->getUuid());
        }
        return null;
    }

}