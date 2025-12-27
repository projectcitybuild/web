<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftConfigController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $config = MinecraftConfig::byLatest()->first();

        return response()->json($config);
    }
}
