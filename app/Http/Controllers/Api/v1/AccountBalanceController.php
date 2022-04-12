<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Models\GameIdentifierType;
use App\Entities\Repositories\MinecraftPlayerRepository;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class AccountBalanceController extends ApiController
{
    public function __construct(
        private MinecraftPlayerRepository $minecraftPlayerRepository,
    ) {}

    public function getBalance(Request $request)
    {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'player_id_type' => ['required', Rule::in(GameIdentifierType::joined())],
                'player_id' => 'required|max:60',
            ],
            messages: [
                'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::joined().']',
            ],
        );

        $playerId = $request->get('player_id');
        $playerType = GameIdentifierType::tryFrom($request->get('player_id_type'));

        if ($playerType !== GameIdentifierType::MINECRAFT_UUID) {
            throw new BadRequestException(id: 'unsupported_player_type', message: 'Unsupported player type');
        }

        $player = $this->minecraftPlayerRepository->getByUuid($playerId)
            ?? throw new NotFoundException(id: 'player_not_found', message: 'Player not found');

        return [
            'balance' => $player->account->balance,
        ];
    }

    public function deductBalance(Request $request)
    {

    }
}
