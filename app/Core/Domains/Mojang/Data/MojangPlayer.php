<?php

namespace App\Core\Domains\Mojang\Data;

/** @deprecated */
class MojangPlayer
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $alias,
        private readonly bool $isLegacyAccount = false,
        private readonly bool $isDemoAccount = false
    ) {}

    /**
     * Player's unique Mojang identifier (UUID).
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Player's in-game name.
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Whether the account has not migrated to a Mojang account.
     */
    public function isLegacyAccount(): bool
    {
        return $this->isLegacyAccount;
    }

    /**
     * Whether the account is a free account (ie. unpaid).
     */
    public function isDemoAccount(): bool
    {
        return $this->isDemoAccount;
    }
}
