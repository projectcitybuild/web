<?php

namespace Domain\PlayerFetch;

use Library\Mojang\Models\MojangPlayer;

interface PlayerFetchAdapter
{
    /**
     * @return MojangPlayer[]
     */
    public function fetch(array $aliases, ?int $timestamp): array;
}
