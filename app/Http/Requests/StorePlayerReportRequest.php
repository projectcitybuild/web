<?php

namespace App\Http\Requests;

use App\Domains\Captcha\Rules\CaptchaRule;
use App\Domains\HoneyPot\Rules\HoneyPotRule;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePlayerReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(CaptchaRule $captchaRule, HoneyPotRule $honeyPotRule): array
    {
        return [
            'reporter_name' => ['required', 'string', 'max:191'],
            'reporter_contact' => ['required', 'string', 'max:191'],
            'reporter_email' => ['nullable', 'email', 'max:191'],
            'reported_player_name' => ['required', 'string', 'max:191'],
            'subject' => ['required', 'string', 'max:150'],
            'report_details' => ['required', 'string', 'min:10', 'max:10000'],
            'visibility' => ['required', Rule::enum(PlayerReportVisibility::class)],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'extensions:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'captcha-response' => ['required', $captchaRule],
            'details' => $honeyPotRule,
        ];
    }
}
