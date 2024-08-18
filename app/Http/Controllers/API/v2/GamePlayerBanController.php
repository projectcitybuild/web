<?php

namespace App\Http\Controllers\API\v2;

use App\Core\Exceptions\BadRequestException;
use App\Http\Controllers\APIController;
use Domain\Bans\Exceptions\AlreadyPermBannedException;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UnbanType;
use Domain\Bans\UseCases\ConvertToPermanentPlayerBan;
use Domain\Bans\UseCases\CreatePlayerBan;
use Domain\Bans\UseCases\CreatePlayerUnban;
use Domain\Bans\UseCases\GetActivePlayerBan;
use Domain\Bans\UseCases\GetAllPlayerBans;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\GamePlayerBanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GamePlayerBanController extends APIController
{
    /**
     * @throws AlreadyPermBannedException
     * @throws BadRequestException
     */
    public function ban(
        Request $request,
        CreatePlayerBan $createBan,
    ): GamePlayerBanResource {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banned_player_alias' => 'required|string',
            'banner_player_id' => 'max:60',
            'banner_player_type' => Rule::in(PlayerIdentifierType::values()),
            'banner_player_alias' => 'string',
            'reason' => 'nullable|string',
            'expires_at' => 'integer',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $expiresAt = $request->get('expires_at');
        if ($expiresAt !== null) {
            $expiresAt = Carbon::createFromTimestamp($expiresAt);

            if ($expiresAt->lt(now())) {
                throw new BadRequestException('bad_input', 'Expiry date cannot be in the past');
            }
        }

        $ban = $createBan->execute(
            serverId: $request->token->server_id,
            bannedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banned_player_type')),
            ),
            bannedPlayerAlias: $request->get('banned_player_alias'),
            bannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
            bannerPlayerAlias: $request->get('banner_player_alias'),
            banReason: $request->get('reason'),
            expiresAt: $expiresAt,
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
            'unbanner_player_id' => 'max:60',
            'unbanner_player_type' => Rule::in(PlayerIdentifierType::values()),
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

    /**
     * @throws AlreadyPermBannedException
     * @throws BadRequestException
     */
    public function convertToPermanent(
        Request $request,
        ConvertToPermanentPlayerBan $convertToPermanentBan,
    ): GamePlayerBanResource {
        $this->validateRequest($request->all(), [
            'ban_id' => 'required|integer',
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banner_player_alias' => 'required',
            'reason' => 'string',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $newBan = $convertToPermanentBan->execute(
            banId: $request->get('ban_id'),
            bannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
            bannerPlayerAlias: $request->get('banner_player_alias'),
            banReason: $request->get('reason'),
        );

        return new GamePlayerBanResource($newBan);
    }

    /**
     * @throws BadRequestException
     */
    public function status(
        Request $request,
        GetActivePlayerBan $getActiveBans,
    ): GamePlayerBanResource|array {
        $this->validateRequest($request->all(), [
            'player_id' => 'required|max:60',
            'player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $ban = $getActiveBans->execute(
            playerIdentifier: new PlayerIdentifier(
                key: $request->get('player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('player_type')),
            ),
        );

        if ($ban === null) {
            return ['data' => null];
        }

        return new GamePlayerBanResource($ban);
    }

    /**
     * @throws BadRequestException
     */
    public function all(
        Request $request,
        GetAllPlayerBans $getAllBans,
    ): AnonymousResourceCollection {
        $this->validateRequest($request->all(), [
            'player_id' => 'required|max:60',
            'player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $bans = $getAllBans->execute(
            playerIdentifier: new PlayerIdentifier(
                key: $request->get('player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('player_type')),
            ),
        );

        return GamePlayerBanResource::collection($bans);
    }
}
