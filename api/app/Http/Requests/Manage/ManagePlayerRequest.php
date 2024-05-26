<?php

namespace App\Http\Requests\Manage;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class ManagePlayerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                'uuid',
            ],
            'account_id' => [
                'integer',
                'exists:'.Account::tableName().','.Account::primaryKey(),
            ],
        ];
    }
}
