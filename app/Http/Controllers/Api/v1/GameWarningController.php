<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Bans\Exceptions\PlayerAlreadyBannedException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\UseCases\ConvertToPermanentBanUseCase;
use Domain\Bans\UseCases\CreateBanUseCase;
use Domain\Bans\UseCases\CreateUnbanUseCase;
use Domain\Bans\UseCases\GetActiveBanUseCase;
use Domain\Bans\UseCases\GetAllBansUseCase;
use Domain\Warnings\UseCases\CreateWarningUseCase;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\GameBanV2Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GameWarningController extends ApiController
{
    /**
     * @throws BadRequestException
     */
    public function store(
        Request $request,
        CreateWarningUseCase $createWarning,
    ): GameBanV2Resource {
        $this->validateRequest($request->all(), [
            'warned_player_id' => 'required|max:60',
            'warned_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'warned_player_alias' => 'required',
            'staff_player_id' => 'required|max:60',
            'staff_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'staff_player_alias' => 'required',
            'reason' => 'required|string',
            'weight' => 'required|float',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $warning = $createWarning->execute(
            warnedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('warned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('warned_player_type')),
            ),
            warnedPlayerAlias: $request->get('warned_player_alias'),
            warnerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('warner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('warner_player_type')),
            ),
            warnerPlayerAlias: $request->get('warner_player_alias'),
            reason: $request->get('reason'),
            weight: $request->get('weight'),
        );

        return new GameBanV2Resource($ban);
    }
}
