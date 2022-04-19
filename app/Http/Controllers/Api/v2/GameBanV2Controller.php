<?php

namespace App\Http\Controllers\Api\v2;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Bans\Exceptions\PlayerAlreadyBannedException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\UseCases\CreateBanUseCase;
use Domain\Bans\UseCases\CreateUnbanUseCase;
use Domain\Bans\UseCases\GetBanUseCase;
use Entities\Models\GameIdentifierType;
use Entities\Resources\GameBanResource;
use Entities\Resources\GameUnbanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GameBanV2Controller extends ApiController
{
    /**
     * @throws PlayerAlreadyBannedException
     * @throws BadRequestException
     */
    public function ban(
        Request $request,
        CreateBanUseCase $createBanUseCase,
    ): GameBanResource {
        $this->validateRequest($request->all(), [
            'server_id' => 'required|integer',
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'banned_player_alias' => 'required',
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'reason' => 'string',
            'expires_at' => 'integer',
            'is_global_ban' => 'required|boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $expiresAt = $request->get('expires_at');
        if ($expiresAt !== null) {
            $expiresAt = Carbon::createFromTimestamp($expiresAt);
        }

        $ban = $createBanUseCase->execute(
            serverId: $request->get('server_id'),
            bannedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: GameIdentifierType::tryFrom($request->get('banned_player_type')),
            ),
            bannedPlayerAlias: $request->get('banned_player_alias'),
            bannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: GameIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
            bannerPlayerAlias: $request->get('banner_player_alias'),
            isGlobalBan: $request->get('is_global_ban', true),
            banReason: $request->get('reason'),
            expiresAt: $expiresAt,
        );

        return new GameBanResource($ban);
    }

    /**
     * @throws PlayerNotBannedException
     * @throws BadRequestException
     */
    public function unban(
        Request $request,
        CreateUnbanUseCase $createUnbanUseCase,
    ): GameUnbanResource {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $unban = $createUnbanUseCase->execute(
            bannedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: GameIdentifierType::tryFrom($request->Get('banned_player_type')),
            ),
            unbannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: GameIdentifierType::tryFrom($request->Get('banner_player_type')),
            ),
        );

        return new GameUnbanResource($unban);
    }

    /**
     * @throws BadRequestException
     */
    public function status(
        Request $request,
        GetBanUseCase $getBanUseCase,
    ): GameBanResource|array {
        $this->validateRequest($request->all(), [
            'player_id' => 'required|max:60',
            'player_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $ban = $getBanUseCase->execute(
            playerIdentifier: new PlayerIdentifier(
                key: $request->get('player_id'),
                gameIdentifierType: GameIdentifierType::tryFrom($request->get('player_id_type')),
            ),
        );

        if ($ban === null) {
            return ['data' => null];
        }
        return new GameBanResource($ban);
    }
}