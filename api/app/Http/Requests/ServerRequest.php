<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->getMethod() == 'POST') {
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
        } else {
            return [
                'name' => 'string|max:255',
                'ip' => 'ip',
                'ip_alias' => 'nullable|string',
                'port' => 'nullable|numeric',
                'is_port_visible' => 'boolean',
                'is_querying' => 'boolean',
                'is_visible' => 'boolean',
                'game_type' => 'numeric',
            ];
        }
    }
}
