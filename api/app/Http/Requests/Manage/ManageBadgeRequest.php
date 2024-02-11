<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ManageBadgeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string'],
            'unicode_icon' => ['required', 'string'],
        ];
    }
}
