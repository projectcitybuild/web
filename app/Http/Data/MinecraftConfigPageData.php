<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

class MinecraftConfigPageData extends Data
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public int $version,
        #[LiteralTypeScriptType('JsonValue')]
        public array $config,
        public string $created_at,
        public string $updated_at,
    ) {}
}
