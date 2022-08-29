@extends('front.templates.master')

@section('title', '2FA Confirmation')
@section('description', '')

@section('body')
    <main class="page login">
        <section class="login__dialog login__mfa login__dialog--is-narrow">
            <h1>2FA Confirmation</h1>

            @include('front.components.form-error')

            <p class="form__description">Enter your current 2FA code to continue</p>
            <form action="{{ route('front.login.mfa.submit') }}" method="post" class="form">
                @csrf
                <div class="form-row form-row--fluid">
                    <label for="code">Enter Code</label>
                    <input type="text" class="textfield textfield--is-mfa" id="code" name="code" maxlength="6"
                           inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" size="6" autofocus>
                </div>

                <button type="submit" class="button button--filled">Verify</button>
                <div class="login__mfa-recovery-link">
                    <a href="{{ route('front.login.mfa-recover') }}">Unable to verify?</a>
                </div>
            </form>
        </section>
    </main>
@endsection
