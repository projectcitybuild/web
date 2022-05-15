<?php

namespace App\Http\Requests;

use Domain\BuilderRankApplications\BuilderRank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BuilderRankApplicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $ranks = array_map(fn ($rank) => $rank->value, BuilderRank::cases());

        return [
            'minecraft_username' => 'required',
            'current_builder_rank' => ['required', Rule::in($ranks)],
            'build_location' => 'required',
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
