<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ManageGroupUpdateRequest extends FormRequest
{
    public function rules(): array
    {
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
