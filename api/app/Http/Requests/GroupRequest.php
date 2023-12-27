<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->getMethod() == 'POST') {
            return [
                'name' => 'required|string',
                'alias' => 'nullable|string',
                'is_build' => 'boolean',
                'is_default' => 'boolean',
                'is_staff' => 'boolean',
                'is_admin' => 'boolean',
                'minecraft_name' => 'nullable|string',
                'discord_name' => 'nullable|string',
                'can_access_panel' => 'boolean',
            ];
        } else {
            return [
                'name' => 'string',
                'alias' => 'string',
                'is_build' => 'boolean',
                'is_default' => 'boolean',
                'is_staff' => 'boolean',
                'is_admin' => 'boolean',
                'minecraft_name' => 'string',
                'discord_name' => 'string',
                'can_access_panel' => 'boolean',
            ];
        }
    }
}
