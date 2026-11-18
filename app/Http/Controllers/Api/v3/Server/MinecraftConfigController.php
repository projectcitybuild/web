<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Http\Controllers\ApiController;
use App\Http\Resources\MinecraftConfigResource;
use App\Models\MinecraftConfig;
use Illuminate\Http\Request;

final class MinecraftConfigController extends ApiController
{
    /**
     * @response array{
     *     id: int,
     *     config: object,
     *     version: int,
     *     created_at: string|null,
     *     updated_at: string|null,
     *     deleted_at: string|null
     * }
     */
    public function __invoke(Request $request): MinecraftConfigResource
    {
        $config = MinecraftConfig::byLatest()->first();

        return new MinecraftConfigResource($config);
    }
}
