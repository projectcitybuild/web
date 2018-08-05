<?php
namespace Domains\Library\QueryPlayer;

interface PlayerQueryAdapterContract
{
    public function getUniqueIdentifiers(array $aliases = []) : array;
    public function createPlayers(array $identifiers) : array;
}