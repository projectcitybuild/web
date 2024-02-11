<?php

namespace App\Http\Requests\Bans;

use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Rules\TimestampPastNow;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlayerBanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banned_player_uuid' => ['required', 'uuid'],
            'banned_player_alias' => 'required|string',
            'banner_player_uuid' => 'max:60',
            'banner_player_alias' => 'string',
            'reason' => 'nullable|string',
            'expires_at' => ['integer', new TimestampPastNow],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->somethingElseIsInvalid()) {
                    $validator->errors()->add(
                        'field',
                        'Something is wrong with this field!'
                    );
                }
            }
        ];
    }
}
