<?php

namespace App\Http\Requests\Manage;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class ManageDonationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => [
                'required',
                'integer',
                'exists:'.Account::tableName().','.Account::primaryKey(),
            ],
            'amount' => ['required', 'numeric'],
        ];
    }
}
