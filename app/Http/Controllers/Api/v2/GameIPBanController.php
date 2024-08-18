<?php

namespace App\Http\Controllers\Api\v2;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Data\PlayerIdentifierType;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Domains\Bans\Exceptions\AlreadyIPBannedException;
use App\Domains\Bans\Exceptions\NotIPBannedException;
use App\Domains\Bans\UnbanType;
use App\Domains\Bans\UseCases\CreateIPBan;
use App\Domains\Bans\UseCases\CreateIPUnban;
use App\Domains\Bans\UseCases\GetActiveIPBan;
use App\Http\Controllers\ApiController;
use App\Http\Resources\GameIPBanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class GameIPBanController extends ApiController
{
    /**
     * @throws AlreadyIPBannedException
     * @throws BadRequestException
     */
    public function ban(
        Request $request,
        CreateIPBan $createIPBan,
    ): GameIPBanResource {
        $this->validateRequest($request->all(), [
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banner_player_alias' => 'required|string',
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $ban = $createIPBan->execute(
            ip: $request->get('ip_address'),
            bannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
            bannerPlayerAlias: $request->get('banner_player_alias'),
            banReason: $request->get('reason'),
        );

        return new GameIPBanResource($ban);
    }

    /**
     * @throws NotIPBannedException
     * @throws BadRequestException
     */
    public function unban(
        Request $request,
        CreateIPUnban $createUnban,
    ): GameIPBanResource {
        $this->validateRequest($request->all(), [
            'ip_address' => 'required|ip',
            'unbanner_player_id' => 'max:60',
            'unbanner_player_type' => Rule::in(PlayerIdentifierType::values()),
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $ban = $createUnban->execute(
            ip: $request->get('ip_address'),
            unbannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('unbanner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('unbanner_player_type')),
            ),
            unbanType: UnbanType::MANUAL,
        );

        return new GameIPBanResource($ban);
    }

    /**
     * @throws BadRequestException
     */
    public function status(
        Request $request,
        GetActiveIPBan $getActiveIPBans,
    ): JsonResponse {
        $this->validateRequest($request->all(), [
            'ip_address' => 'required|ip',
        ]);
        $ban = $getActiveIPBans->execute(ip: $request->get('ip_address'));

        if ($ban === null) {
            return response()->json(['data' => []]);
        }

        return response()->json([
            'data' => GameIPBanResource::make($ban),
        ]);
    }
}
