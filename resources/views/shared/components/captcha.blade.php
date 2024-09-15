@push('head')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush

<div
    {{ $attributes->merge(['class' => 'cf-turnstile']) }}
    data-response-field-name="captcha-response"
    data-size="flexible"
    data-sitekey="@captcha_key"
    data-feedback-enabled="false"
></div>
