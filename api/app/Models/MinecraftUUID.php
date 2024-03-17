<?php

namespace App\Models;

use Exception;

/**
 * Wrapper for transferring around a Minecraft UUID - agnostic of what format the UUID is in.
 * Supports converting between formats on demand.
 */
class MinecraftUUID
{
    const PATTERN_FULL = '#([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})#';
    const PATTERN_TRIMMED = '#([0-9a-fA-F]{32})#';

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
            throw new Exception($uuid . ' is not a valid Minecraft UUID format');
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
}
