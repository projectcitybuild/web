<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\Http\BadRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Entities\GameIdentifierType;

final class GameUnbanStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'player_id_type'    => ['required', Rule::in(GameIdentifierType::identifierMappingStr())],
            'player_id'         => 'required',
            'banner_id_type'    => ['required', Rule::in(GameIdentifierType::identifierMappingStr())],
            'banner_id'         => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() : array
    {
        return [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::identifierMappingStr().']',
        ];
    }

     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new BadRequestException('bad_input', $validator->errors()->first());
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }
}
