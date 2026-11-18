<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class MinecraftConfigPageData extends Data
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public int $version,
        public array $config,
        public string $created_at,
        public string $updated_at,
    ) {}
}
