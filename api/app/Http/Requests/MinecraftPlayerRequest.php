<?php

namespace App\Http\Requests;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Http\FormRequest;

class MinecraftPlayerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => 'required|uuid',
            'account_id' => 'integer|exists:'.Account::tableName().','.Account::primaryKey(),
        ];
    }
}
