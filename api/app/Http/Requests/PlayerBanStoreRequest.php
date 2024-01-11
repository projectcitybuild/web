<?php

namespace App\Http\Requests;

use App\Models\Eloquent\PlayerBan;
use App\Models\Eloquent\Player;
use App\Rules\TimestampPastNow;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class PlayerBanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banned_player_id' => [
                'required',
                'integer',
                'exists:'.Player::tableName().','.Player::primaryKey(),
                function (string $attribute, mixed $value, Closure $fail) {
                    if (PlayerBan::where('banned_player_id', $value)
                        ->active()
                        ->exists()) {
                        $fail("This player is already banned");
                    }
                },
            ],
            'banned_player_alias' => ['required', 'string'],
            'banner_player_id' => [
                'integer',
                'exists:'.Player::tableName().','.Player::primaryKey(),
            ],
            'banner_player_alias' => 'string',
            'reason' => ['nullable', 'string'],
            'expires_at' => ['integer', new TimestampPastNow],
        ];
    }
}
