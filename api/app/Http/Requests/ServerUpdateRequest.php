<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServerUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'ip' => 'ip',
            'ip_alias' => ['nullable', 'string'],
            'port' => ['nullable', 'numeric'],
            'is_port_visible' => 'boolean',
            'is_querying' => 'boolean',
            'is_visible' => 'boolean',
            'game_type' => 'numeric',
        ];
    }
}
