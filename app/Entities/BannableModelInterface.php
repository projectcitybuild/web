<?php

namespace App\Entities;

interface BannableModelInterface
{
    /**
     * Returns the data of a field used to identify a player
     * in banning.
     *
     * For example, for Minecraft return the UUID field
     */
    public function getBanIdentifier(): string;

    /**
     * Returns the human-readable, display name.
     * Returns null if no alias is known.
     */
    public function getBanReadableName(): ?string;
}
