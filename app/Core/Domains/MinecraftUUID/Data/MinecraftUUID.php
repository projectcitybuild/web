<?php

namespace App\Core\Domains\MinecraftUUID\Data;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Wrapper for transferring around a Minecraft UUID, regardless of whether the hyphens are trimmed.
 *
 * Supports converting between formats on demand.
 */
class MinecraftUUID implements CastsAttributes
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
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     * @throws Exception if invalid Minecraft UUID
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return new MinecraftUUID($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (! $value instanceof MinecraftUUID) {
            throw new \InvalidArgumentException(
                sprintf('Value must be of type %s', MinecraftUUID::class)
            );
        }
        return $model->trimmed();
    }
}
