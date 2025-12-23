<?php

namespace App\Core\Domains\MinecraftUUID\Casts;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class MinecraftUUIDCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @return mixed
     *
     * @throws Exception if invalid Minecraft UUID
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return new MinecraftUUID($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (! $value instanceof MinecraftUUID) {
            throw new InvalidArgumentException('Value must be of type MinecraftUUID');
        }
        return $value->trimmed();
    }
}
