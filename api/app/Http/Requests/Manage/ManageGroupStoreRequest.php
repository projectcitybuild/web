<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ManageGroupStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'alias' => ['nullable', 'string'],
            'is_build' => 'boolean',
            'is_default' => 'boolean',
            'is_staff' => 'boolean',
            'is_admin' => 'boolean',
            'minecraft_name' => ['nullable', 'string'],
            'discord_name' => ['nullable', 'string'],
            'can_access_panel' => 'boolean',
        ];
    }
}
