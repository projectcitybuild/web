<?php

namespace App\Library\Mojang\Api;

use GuzzleHttp\Client;
use App\Exceptions\Http\TooManyRequestsException;
use GuzzleHttp\Exception\ClientException;
use App\Library\Mojang\Models\MojangPlayer;
use App\Library\Mojang\Models\MojangPlayerNameHistory;

final class MojangPlayerApi implements MojangPlayerApiContract
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function getUuidOf(string $name, ?int $time = null) : ?MojangPlayer
    {
        if (is_null($time)) {
            $time = time();
        }
        
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.mojang.com/users/profiles/minecraft/' . $name, [
                'query' => [
                    'at' => $time,
                ],
            ]);
        } 
        catch (ClientException $e) {
            if ($e->getCode() === 429) {
                throw new TooManyRequestsException('rate_limited', 'Too many requests sent to the Mojang API');
            }
            throw $e;
        }

        // if no player exists, return null
        if ($response->getStatusCode() === 204) {
            return null;
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
     * @inheritDoc
     */
    public function getOriginalOwnerUuidOf(string $name) : ?MojangPlayer
    {
        return $this->getUuidOf($name, 0);
    }

    /**
     * @inheritDoc
     */
    public function getUuidBatchOf(array $names) : ?array
    {
        if (count($names) === 0 || count($names) > 100) {
            throw new \Exception('Batch must contain between 1 and 100 names to search');
        }

        // Check for invalid names before hitting the api. The entire request will fail midway
        // if one of the batches containts an empty name
        foreach ($names as $name) {
            if (empty($name)) {
                throw new \Exception('Name cannot be null or empty');
            }
        }

        $response = null;
        try {
            $response = $this->client->request('POST', 'https://api.mojang.com/profiles/minecraft', [
                'json' => $names,
            ]);
        } 
        catch (ClientException $e) {
            if ($e->getCode() === 429) {
                throw new TooManyRequestsException('rate_limited', 'Too many requests sent to the Mojang API');
            }
            throw $e;
        }

        $data = json_decode($response->getBody());
        if (count($data) === 0) {
            return [];
        }

        return collect($data)
            ->keyBy(function ($player) {
                return $player->name;
            })
            ->map(function ($player) {
                return new MojangPlayer(
                    $player->id,
                    $player->name,
                    isset($player->legacy),
                    isset($player->demo)
                );
            })
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getNameHistoryOf($uuid) : ?MojangPlayerNameHistory
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.mojang.com/user/profiles/' . $uuid . '/names');
        } catch (ClientException $e) {
            if ($e->getCode() === 429) {
                throw new TooManyRequestsException('rate_limited', 'Too many requests sent to the Mojang API');
            }
            throw $e;
        }

        // if no player exists, return null
        if ($response->getStatusCode() === 204) {
            return null;
        }

        return new MojangPlayerNameHistory(
            json_decode($response->getBody())
        );
    }

    /**
     * @inheritDoc
     */
    public function getNameHistoryByNameOf($name) : ?MojangPlayerNameHistory
    {
        $player = $this->getUuidOf($name);
        if ($player !== null) {
            return $this->getNameHistoryOf($player->getUuid());
        }
        return null;
    }
}
