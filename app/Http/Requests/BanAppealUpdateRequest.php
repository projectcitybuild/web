<?php

namespace App\Http\Requests;

use Domain\BanAppeals\Entities\BanAppealStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BanAppealUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private function allowedStatuses()
    {
        return collect(BanAppealStatus::decisionCases())
            ->map(fn($item, $key) => $item->value);
    }

    public function rules(): array
    {
        return [
            'decision_note' => 'required',
            'status' => ['required', Rule::in($this->allowedStatuses())]
        ];
    }
}
