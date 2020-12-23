<?php

namespace App\Library\Mojang\Models;

class MojangPlayerNameChange
{
    /**
     * In-game alias
     */
    private string $alias;

    /**
     * Timestamp of when this name was switched to
     */
    private int $changedToAt;

    /**
     * Whether the alias is their current name in use
     */
    private bool $isCurrent;

    public function __construct(string $alias,
                                ?int $changedToAt = null,
                                bool $isCurrent = false)
    {
        $this->alias = $alias;
        $this->changedToAt = $changedToAt;
        $this->isCurrent = $isCurrent;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getChangeDate(): ?int
    {
        if ($this->changedToAt !== null) {
            return $this->changedToAt / 1000;
        }
        return null;
    }

    public function isCurrentAlias(): bool
    {
        return $this->isCurrent;
    }

    public function isOriginalAlias(): bool
    {
        return $this->changedToAt === null;
    }
}
