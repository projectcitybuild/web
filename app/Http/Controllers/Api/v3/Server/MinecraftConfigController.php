<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Http\Controllers\ApiController;
use App\Http\Data\MinecraftConfigApiData;
use App\Models\MinecraftConfig;
use Illuminate\Http\Request;

final class MinecraftConfigController extends ApiController
{
    public function __invoke(Request $request): MinecraftConfigApiData
    {
        $config = MinecraftConfig::byLatest()->first();

        return MinecraftConfigApiData::fromModel($config);
    }
}
