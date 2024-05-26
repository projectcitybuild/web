<?php

namespace App\Http\Requests\Bans;

use App\Core\MinecraftUUID\MinecraftUUID;
use App\Domains\Bans\Transfers\CreatePlayerBanTransfer;
use App\Utilities\Rules\TimestampPastNow;
use App\Utilities\Traits\HasTimestampInput;
use Illuminate\Foundation\Http\FormRequest;

class PlayerBanStoreRequest extends FormRequest
{
    use HasTimestampInput;

    public function authorize(): bool
    {
        return true;
    }

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

    public function playerBan(): CreatePlayerBanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreatePlayerBanTransfer(
            bannedPlayerUUID: new MinecraftUUID($validated->get('banned_player_uuid')),
            bannedPlayerAlias: $validated->get('banned_player_alias'),
            bannerPlayerUUID: $validated->has('banner_player_uuid')
                ? new MinecraftUUID($validated->get('banner_player_uuid'))
                : null,
            reason: $validated->get('reason'),
            expiresAt: self::timestamp(from: $validated->get('expires_at')),
        );
    }
}
