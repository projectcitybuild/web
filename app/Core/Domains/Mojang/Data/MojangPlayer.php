<?php

namespace App\Core\Domains\Mojang\Data;

class MojangPlayer
{
    /**
     * Player's unique Mojang identifier (UUID).
     * TODO: this should use Entities\MinecraftUUID
     *
     * @var string
     */
    private $uuid;

    /**
     * Player's in-game name.
     *
     * @var string
     */
    private $alias;

    /**
     * Whether the account has not migrated to a Mojang account.
     *
     * @var bool
     */
    private $isLegacyAccount;

    /**
     * Whether the account is a free account (ie. unpaid).
     *
     * @var bool
     */
    private $isDemoAccount;

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
