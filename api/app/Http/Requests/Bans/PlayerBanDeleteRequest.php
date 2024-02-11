<?php

namespace App\Http\Requests\Bans;

use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Rules\TimestampPastNow;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlayerBanDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banned_player_uuid' => ['required', 'uuid'],
            'unbanner_player_uuid' => ['uuid'],
            'reason' => 'string',
        ];
    }
}
