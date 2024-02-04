<?php

namespace App\Http\Requests;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Http\FormRequest;

class DonationRequest extends FormRequest
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
