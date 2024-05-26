<?php

namespace App\Http\Requests\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Core\Rules\TimestampPastNow;
use App\Core\Traits\HasTimestampInput;
use App\Domains\Bans\Transfers\CreatePlayerBanTransfer;
use Illuminate\Foundation\Http\FormRequest;

class PlayerBanStoreRequest extends FormRequest
{
    use HasTimestampInput;

    public function rules(): array
    {
        return [
            'banned_player_uuid' => ['required', 'uuid'],
            'banned_player_alias' => 'required|string',
            'banner_player_uuid' => 'uuid',
            'reason' => 'string',
            'expires_at' => ['integer', new TimestampPastNow],
        ];
    }

    public function transfer(): CreatePlayerBanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreatePlayerBanTransfer(
            bannedPlayerUUID: new MinecraftUUID($validated->get('banned_player_uuid')),
            bannedPlayerAlias: $validated->get('banned_player_alias'),
            bannerPlayerUUID: optional(
                $validated->get('banner_player_uuid'),
                fn ($uuid) => new MinecraftUUID($uuid),
            ),
            reason: $validated->get('reason'),
            expiresAt: self::timestamp(from: $validated->get('expires_at')),
        );
    }
}
