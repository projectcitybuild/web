<?php

namespace App\Http\Controllers\Api\v2;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Bans\Exceptions\PlayerAlreadyBannedException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\UseCases\CreateBanUseCase;
use Domain\Bans\UseCases\CreateUnbanUseCase;
use Domain\Bans\UseCases\GetActiveBanUseCase;
use Domain\Bans\UseCases\GetAllBansUseCase;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\GameBanV2Resource;
use Entities\Resources\GameUnbanV2Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        CreateBanUseCase $createBan,
    ): GameBanV2Resource {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banned_player_alias' => 'required',
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banner_player_alias' => 'required',
            'reason' => 'string',
            'expires_at' => 'integer',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $expiresAt = $request->get('expires_at');
        if ($expiresAt !== null) {
            $expiresAt = Carbon::createFromTimestamp($expiresAt);

            if ($expiresAt->lt(now()->getTimestamp())) {
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

        return new GameBanV2Resource($ban);
    }

    /**
     * @throws PlayerNotBannedException
     * @throws BadRequestException
     */
    public function unban(
        Request $request,
        CreateUnbanUseCase $createUnban,
    ): GameUnbanV2Resource {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'banner_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $unban = $createUnban->execute(
            bannedPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banned_player_type')),
            ),
            unbannerPlayerIdentifier: new PlayerIdentifier(
                key: $request->get('banner_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
        );

        return new GameUnbanV2Resource($unban);
    }

    /**
     * @throws BadRequestException
     */
    public function status(
        Request $request,
        GetActiveBanUseCase $getActiveBans,
    ): GameBanV2Resource|array {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $ban = $getActiveBans->execute(
            playerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
        );

        if ($ban === null) {
            return ['data' => null];
        }

        return new GameBanV2Resource($ban);
    }

    /**
     * @throws BadRequestException
     */
    public function all(
        Request $request,
        GetAllBansUseCase $getBans,
    ): AnonymousResourceCollection {
        $this->validateRequest($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banner_player_type' => ['required', Rule::in(PlayerIdentifierType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $bans = $getBans->execute(
            playerIdentifier: new PlayerIdentifier(
                key: $request->get('banned_player_id'),
                gameIdentifierType: PlayerIdentifierType::tryFrom($request->get('banner_player_type')),
            ),
        );

        return GameBanV2Resource::collection($bans);
    }
}
