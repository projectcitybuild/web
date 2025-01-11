<?php

namespace App\Http\Requests;

use App\Domains\BuilderRankApplications\Data\BuilderRank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BuilderRankApplicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $ranks = collect(BuilderRank::cases())
            ->map(fn ($rank) => $rank->value)
            ->toArray();

        return [
            'minecraft_username' => 'required',
            'current_builder_rank' => ['required', Rule::in($ranks)],
            'build_location' => 'required',
            'build_description' => 'required',
            'additional_notes' => 'nullable',
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
