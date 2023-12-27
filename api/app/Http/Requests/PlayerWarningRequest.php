<?php

namespace App\Http\Requests;

use App\Models\Eloquent\Player;
use Illuminate\Foundation\Http\FormRequest;

class PlayerWarningRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'warned_player_id' => [
                'required',
                'exists:'.Player::tableName().','.Player::primaryKey(),
            ],
            'warned_player_alias' => 'required',
            'warner_player_id' => [
                'required',
                'exists:'.Player::tableName().','.Player::primaryKey(),
            ],
            'warner_player_alias' => 'required',
            'reason' => 'required|string',
            'weight' => 'required|integer',
            'acknowledged_at' => 'integer',
        ];
    }
}
