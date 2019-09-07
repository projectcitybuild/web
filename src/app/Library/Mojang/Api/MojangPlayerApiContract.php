<?php
namespace App\Library\Mojang\Api;

use App\Exceptions\Http\TooManyRequestsException;
use App\Library\Mojang\Models\MojangPlayer;
use App\Library\Mojang\Models\MojangPlayerNameHistory;

interface MojangPlayerApiContract
{
    /**
     * Retrieves the UUID that belongs to the given name at the given time.
     * The name returned is the current name of the player who owned the name at the time.
     *
     * If no time given, uses the current time.
     *
     * @param string $name
     * @param int|null $time
     *
     * @return MojangPlayer
     * @throws TooManyRequestsException
     */
    function getUuidOf(string $name, ?int $time = null) : ?MojangPlayer;

    /**
     * Retrieves the UUID for the person who first registered
     * the given name, regardless of who currently owns it now.
     *
     * @param $name
     *
     * @return MojangPlayer
     * @throws TooManyRequestsException
     */
    function getOriginalOwnerUuidOf(string $name) : ?MojangPlayer;

    /**
     * Retrieves UUIDs for every name in the given array, in a
     * single lookup.
     *
     * The API only allows a max of 100 names per lookup.
     *
     * @param array $names
     *
     * @return array
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    function getUuidBatchOf(array $names) : ?array;

    /**
     * Returns all the usernames this user has used in the past and
     * the one they are using currently.
     *
     * The UUID must be given without hyphens.
     *
     * @param $uuid
     *
     * @return array|null
     * @throws TooManyRequestsException
     */
    function getNameHistoryOf($uuid) : ?MojangPlayerNameHistory;

    /**
     * Returns all the usernames this user has used in the past and
     * the one they are using currently.
     *
     * Performs two lookups as the original API can only be queried using an UUID.
     *
     * @param $name
     *
     * @return array|null
     * @throws TooManyRequestsException
     */
    function getNameHistoryByNameOf($name) : ?MojangPlayerNameHistory;
}
