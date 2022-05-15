<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BuilderRankApplicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'minecraft_username' => 'required',
            'current_builder_rank' => 'required',
            'build_location' => 'required',    // discourse min is 8 or greater
            'build_description' => 'required',
            'g-recaptcha-response' => 'recaptcha',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
