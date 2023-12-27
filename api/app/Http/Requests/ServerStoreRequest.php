<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServerStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'ip' => 'required|ip',
            'ip_alias' => 'nullable|string',
            'port' => 'nullable|numeric',
            'is_port_visible' => 'required|boolean',
            'is_querying' => 'required|boolean',
            'is_visible' => 'required|boolean',
            'game_type' => 'required|numeric',
        ];
    }
}
