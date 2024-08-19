<?php

namespace App\Core\Domains\PlayerLookup;

use App\Models\Account;

/**
 * @deprecated
 */
interface Player
{
    /**
     * @return int The ID of the Eloquent model
     */
    public function getKey();

    /**
     * @return Account|null The PCB account linked to this player
     */
    public function getLinkedAccount(): ?Account;

    /**
     * @return mixed The original Eloquent model itself
     */
    public function getRawModel(): static;
}