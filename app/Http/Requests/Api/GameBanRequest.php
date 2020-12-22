<?php

namespace App\Http\Requests\Api;

use App\Exceptions\Http\BadRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

final class GameBanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'player_id_type' => 'required',
            'player_id' => 'required|max:60',
            'player_alias' => 'required',
            'staff_id_type' => 'required',
            'staff_id' => 'required|max:60',
            'reason' => 'string',
            'expires_at' => 'integer',
            'is_global_ban' => 'boolean',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new BadRequestException('bad_input', $validator->errors()->first());
    }
}
