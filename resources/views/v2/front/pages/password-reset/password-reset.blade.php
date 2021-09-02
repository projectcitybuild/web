@extends('front.layouts.master')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Forgot Your Password?</h1>

            @if(Session::has('success'))
                <div class="alert alert--success">
                    <h3><i class="fas fa-exclamation-circle"></i> Success</h3>
                    An email has been sent to {{ Session::get('success') }} with password reset instructions.
                </div>
                <p>
            @endif

            <p>
                Please enter the email address you used to create your account.
                If the account exists, an email will be sent with a link to change your password.
            </p>

            <form method="post" action="{{ route('front.password-reset.store') }}" id="form">
                @csrf

                @include('v2.front.components.form-error')

                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="Email Address" value="{{ old('email') }}" />
                </div>

                <div>
                    <button
                        class="g-recaptcha button button--large button--fill button--primary"
                        data-sitekey="@recaptcha_key"
                        data-callback="submitForm"
                        >
                        <i class="fas fa-envelope"></i> Send Reset Link
                    </button>
                </div>

            </form>
        </div>

    </div>

@endsection
