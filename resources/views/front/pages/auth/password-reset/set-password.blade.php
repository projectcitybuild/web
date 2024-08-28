@extends('front.pages.auth.layout')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@section('content')
    <main class="page login">
        <div class="login__dialog login__dialog--is-narrow">
            <h1>Set Your Password</h1>
            <p>Please enter a new password for your account.</p>

            <form method="post" action="{{ route('front.password-reset.update') }}" class="form">
                @method('PATCH')
                @csrf

                @include('front.components.form-error')

                <input type="hidden" name="password_token" value="{{ $passwordToken }}"/>

                <div class="form-row">
                    <label for="password">Password</label>
                    <input class="textfield {{ $errors->any() ? 'input-text--error' : '' }}" name="password"
                           type="password" placeholder="Password"/>
                </div>

                <div class="form-row">
                    <label for="password_confirm">Password Confirm</label>
                    <input class="textfield {{ $errors->any() ? 'input-text--error' : '' }}" name="password_confirm"
                           type="password" placeholder="Password"/>
                </div>

                <button class="button button--filled button--block" type="submit">
                    <i class="fas fa-check"></i> Reset Password
                </button>

            </form>
        </div>
    </main>

    @include('front.components.footer')
@endsection
