<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Badges\UseCases\GetBadges;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftBadgeController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetBadges $getBadges,
    ) {
        $uuid = new MinecraftUUID($uuid);
        $badges = $getBadges->execute(uuid: $uuid);

        return [
            'data' => $badges,
        ];
    }
}
