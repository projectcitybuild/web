<?php

namespace App\Library\Mojang\Models;

class MojangPlayerNameHistory
{
    /**
     * An array of all name changes for the player
     *
     * @var array<MojangPlayerNameChange>
     */
    private $nameChanges = [];

    public function __construct(array $nameChanges)
    {
        for ($i = 0; $i < count($nameChanges); $i++) {
            $nameChange = $nameChanges[$i];
            $this->nameChanges[] = new MojangPlayerNameChange(
                $nameChange->name,
                property_exists($nameChange, 'changedToAt') ? $nameChange->changedToAt : null,
                $i + 1 === count($nameChanges)
            );
        }
    }

    /**
     * @return array<MojangPlayerNameChange>
     */
    public function getNameChanges(): array
    {
        return $this->nameChanges;
    }
}
