<?php

namespace App\Core\Domains\Mojang\Api;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Domains\Mojang\Data\MojangPlayer;
use App\Core\Domains\Mojang\Data\MojangPlayerNameHistory;
use App\Models\MinecraftPlayer;
use GuzzleHttp\Client;
use Symfony\Component\HttpClient\Exception\ClientException;
use function collect;

/**
 * @deprecated Use LookupMinecraftUUID instead
 */
class MojangPlayerApi
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Retrieves the UUID that belongs to the given name at the given time.
     * The name returned is the current name of the player who owned the name at the time.
     *
     * If no time given, uses the current time.
     *
     * @return MojangPlayer
     * @throws TooManyRequestsException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getUuidOf(string $name, ?int $time = null): ?MojangPlayer
    {
        if (is_null($time)) {
            $time = time();
        }

        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.mojang.com/users/profiles/minecraft/'.$name, [
                'query' => [
                    'at' => $time,
                ],
            ]);
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
     * @throws TooManyRequestsException
     */
    public function getOriginalOwnerUuidOf(string $name): ?MojangPlayer
    {
        return $this->getUuidOf($name, 0);
    }

    /**
     * Retrieves UUIDs for every name in the given array, in a
     * single lookup.
     *
     * The API only allows a max of 10 names per lookup.
     *
     * @return MinecraftPlayer[]
     * @throws TooManyRequestsException
     */
    public function getUuidBatchOf(array $names): ?array
    {
        if (count($names) === 0 || count($names) > 10) {
            throw new \Exception('Batch must contain between 1 and 10 names to search');
        }

        // Just in case a dictionary was given to us, use only the values
        $names = array_values($names);

        // Strip empty names from the batch or else the API will return an error
        $names = array_filter($names, fn ($name) => ! empty($name));

        $response = null;
        try {
            $response = $this->client->request('POST', 'https://api.mojang.com/profiles/minecraft', [
                'json' => $names,
            ]);
        } catch (ClientException $e) {
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
                    str_replace('-', '', $player->id),
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
     * @throws TooManyRequestsException
     */
    public function getNameHistoryOf($uuid): ?MojangPlayerNameHistory
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.mojang.com/user/profiles/'.$uuid.'/names');
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
     * Returns all the usernames this user has used in the past and
     * the one they are using currently.
     *
     * Performs two lookups as the original API can only be queried using an UUID.
     *
     * @param $name
     * @return array|null
     * @throws TooManyRequestsException
     */
    public function getNameHistoryByNameOf($name): ?MojangPlayerNameHistory
    {
        $player = $this->getUuidOf($name);
        if ($player !== null) {
            return $this->getNameHistoryOf($player->getUuid());
        }

        return null;
    }
}
