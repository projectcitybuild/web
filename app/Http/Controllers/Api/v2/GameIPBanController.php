<?php

namespace App\Http\Controllers\Api\v2;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Bans\Exceptions\AlreadyIPBannedException;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UnbanType;
use Domain\Bans\UseCases\CreateIPBan;
use Domain\Bans\UseCases\CreatePlayerUnban;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\GamePlayerBanResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GameIPBanController extends ApiController
{
    /**
     * @throws AlreadyIPBannedException
     * @throws BadRequestException
     */
    public function ban(
        Request $request,
        CreateIPBan $createIPBan,
    ): GamePlayerBanResource {
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

        return new GamePlayerBanResource($ban);
    }

    /**
     * @throws NotBannedException
     * @throws BadRequestException
     */
    public function unban(
        Request $request,
        CreatePlayerUnban $createUnban,
    ): GamePlayerBanResource {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'ip' => 'required|ip',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $ban = $createUnban->execute(
            bannedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banned_player_type')),
            ),
            unbannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('unbanner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('unbanner_player_type')),
            ),
            unbanType: UnbanType::MANUAL,
        );

        return new GamePlayerBanResource($ban);
    }
}
