<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Repositories\MinecraftPlayerRepository;
use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Illuminate\Http\Request;

final class MinecraftBalanceController extends ApiController
{
    public function __construct(
        private MinecraftPlayerRepository $minecraftPlayerRepository,
    ) {}

    public function show(Request $request, string $uuid)
    {
        $uuid = str_replace(search: '-', replace: '', subject: $uuid);

        $player = $this->minecraftPlayerRepository->getByUuid($uuid)
            ?? throw new NotFoundException(id: 'player_not_found', message: 'Player not found');

        $account = $player->account
            ?? throw new NotFoundException('player_not_linked', 'Minecraft player not linked to an account');

        return [
            'balance' => $account->balance,
        ];
    }

    public function deduct(Request $request)
    {

    }
}
