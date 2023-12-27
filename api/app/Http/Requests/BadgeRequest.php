<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
        ];
    }
}
