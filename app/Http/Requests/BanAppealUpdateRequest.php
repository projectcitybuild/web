<?php

namespace App\Http\Requests;

use App\Domains\BanAppeals\Data\BanAppealStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BanAppealUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private function allowedStatuses(): Collection
    {
        return collect(BanAppealStatus::decisionCases())
            ->map(fn ($item, $key) => $item->value);
    }

    public function rules(): array
    {
        return [
            'decision_note' => 'required',
            'status' => ['required', Rule::in($this->allowedStatuses())],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->user()->minecraftAccount()->exists()) {
                $validator->errors()->add(
                    'error',
                    'You must have a linked Minecraft account.'
                );
            }
        });
    }
}
