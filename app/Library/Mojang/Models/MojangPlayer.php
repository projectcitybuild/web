<?php

namespace App\Library\Mojang\Models;

class MojangPlayer
{
    /**
     * Player's unique Mojang identifier (UUID)
     */
    private string $uuid;

    /**
     * Player's in-game name
     */
    private string $alias;

    /**
     * Whether the account has not migrated to a Mojang account
     */
    private bool $isLegacyAccount;

    /**
     * Whether the account is a free account (ie. unpaid)
     */
    private bool $isDemoAccount;

    public function __construct(string $uuid,
                                string $alias,
                                bool $isLegacyAccount = false,
                                bool $isDemoAccount = false)
    {
        $this->uuid = $uuid;
        $this->alias = $alias;
        $this->isLegacyAccount = $isLegacyAccount;
        $this->isDemoAccount = $isDemoAccount;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function isLegacyAccount(): bool
    {
        return $this->isLegacyAccount;
    }

    public function isDemoAccount(): bool
    {
        return $this->isDemoAccount;
    }
}
