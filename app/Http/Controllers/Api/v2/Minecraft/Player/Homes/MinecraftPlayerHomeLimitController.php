<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Homes;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Homes\Services\HomeService;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftPlayerHomeLimitController extends ApiController
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function __invoke(Request $request, MinecraftUUID $minecraftUUID)
    {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404, 'Player not found');

        $count = $this->homeService->count($player);

        return [
            'current' => $count->used,
            'max' => $count->allowed,
            'sources' => $count->sources,
        ];
    }
}
