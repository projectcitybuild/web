@extends('front.templates.master')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('body')
    <main class="page login">
        <div class="login__dialog login__dialog--is-narrow">
            <h1>Create an Account</h1>
            <form method="post" action="{{ route('front.register.submit') }}" id="form" class="form">
                @csrf

                @include('front.components.form-error')

                <div class="form-row">
                    <label for="email">Email Address</label>
                    <input
                        class="textfield {{ $errors->any() ? 'error' : '' }}"
                        name="email"
                        id="email"
                        type="email"
                        placeholder="Email Address"
                        value="{{ old('email') }}"
                    />
                </div>
                <div class="form-row">
                    <label for="username">Username</label>
                    <input
                        class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                        name="username"
                        id="username"
                        type="text"
                        placeholder="Username"
                        value="{{ old('username') }}"
                    />
                </div>
                <div class="form-row">
                    <label for="password">Password</label>
                    <input
                        class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                        name="password"
                        id="password"
                        type="password"
                        placeholder="Password"
                        value="{{ old('password') }}"
                    />
                </div>
                <div class="form-row">
                    <input
                        class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                        id="password_confirm"
                        aria-label="Confirm Password"
                        name="password_confirm"
                        type="password"
                        placeholder="Password (Confirm)"
                        value="{{ old('password_confirm') }}"
                    />
                </div>

                <div class="form-row form-row--emphasise-checkbox">
                    <label>
                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            {{ old('terms') ? 'checked' : '' }}
                        >
                        I agree to the
                        <a href="{{ route('terms') }}" target="_blank">terms and conditions</a> and
                        <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.
                    </label>
                </div>

                <button
                    class="g-recaptcha button button--filled button--block"
                    data-sitekey="@recaptcha_key"
                    data-callback="submitForm"
                >
                    <i class="fas fa-chevron-right"></i> Register
                </button>
            </form>
        </div>
    </main>

    @include('front.components.footer')
@endsection
