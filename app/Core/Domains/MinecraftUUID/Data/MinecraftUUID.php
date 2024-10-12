<?php

namespace App\Core\Domains\MinecraftUUID\Data;

use App\Core\Domains\MinecraftUUID\Casts\MinecraftUUIDCast;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Wrapper for transferring around a Minecraft UUID.
 *
 * - Can be constructed regardless of whether hyphens are present
 * - Can convert between formats on demand
 */
class MinecraftUUID implements Castable, Arrayable
{
    private const PATTERN_FULL = '/^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})$/';
    private const PATTERN_TRIMMED = '/^([0-9a-fA-F]{32})$/';

    private string $trimmed;

    /**
     * @throws Exception if $uuid is not a valid Minecraft UUID format
     */
    public function __construct(string $uuid)
    {
        if (preg_match(self::PATTERN_FULL, $uuid, $matches) > 0) {
            $this->trimmed = trim(str_replace(search: '-', replace: '', subject: $uuid));
        } else if (preg_match(self::PATTERN_TRIMMED, $uuid, $matches) > 0) {
            $this->trimmed = trim($uuid);
        } else {
            Log::warning('Could not parse invalid Minecraft UUID', [
                'uuid' => $uuid,
            ]);
            throw ValidationException::withMessages([
                'uuid' => 'Invalid Minecraft UUID format',
            ]);
        }
    }

    public static function tryParse(string $uuid): ?self
    {
        try {
            return new self($uuid);
        } catch (Exception $e) {
            return null;
        }
    }

    public function __toString(): string
    {
        return $this->trimmed;
    }

    public function toArray(): array|string
    {
        // Object is output as {} in a Response without this
        return $this->trimmed();
    }

    public function trimmed(): string
    {
        return $this->trimmed;
    }

    public function full(): string
    {
        $uuid = $this->trimmed;
        // Note: despite the function name, it's inserting not replacing
        $uuid = substr_replace($uuid, replace: '-', offset: 8, length: 0);
        $uuid = substr_replace($uuid, replace: '-', offset: 13, length: 0);
        $uuid = substr_replace($uuid, replace: '-', offset: 18, length: 0);
        $uuid = substr_replace($uuid, replace: '-', offset: 23, length: 0);
        return $uuid;
    }

    /**
     * Returns the class responsible for Eloquent casting.
     *
     * By defining this, we can cast a model's column to MinecraftUUID, instead of MinecraftUUIDCast
     *
     * @param array $arguments
     * @return string
     */
    public static function castUsing(array $arguments): string
    {
        return MinecraftUUIDCast::class;
    }
}
