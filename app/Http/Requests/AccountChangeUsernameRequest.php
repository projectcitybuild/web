<?php

namespace App\Http\Requests;

use App\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;

class AccountChangeUsernameRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     */
    protected string $errorBag = 'username';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'unique:accounts,username', new DiscourseUsernameRule()],
        ];
    }

    /**
     * Redirect back to the form anchor
     */
    protected function getRedirectUrl(): string
    {
        $url = $this->redirector->getUrlGenerator();
        return $url->previous() . '#change-username';
    }
}
