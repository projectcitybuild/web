@extends('v2.front.templates.master')

@section('title', '2FA Recovery')
@section('description', '')

@section('body')
    <main class="page login">
        <section class="login__dialog login__mfa-recover login__dialog--is-narrow">
            <h1>Recover MFA</h1>

            @include('v2.front.components.form-error')

            <p class="form__description">
                If you've lost access to your 2FA device, you can disable 2FA on your account by entering your backup code.
            </p>

            <form action="{{ route('front.login.mfa-recover.submit') }}" class="form" method="post">
                @csrf
                @method('DELETE')

                <div class="form-row">
                    <label for="backup_code">Enter backup code</label>
                    <input type="text" class="textfield" name="backup_code" id="backup_code">
                </div>

                <button type="submit" class="button button--filled">Recover</button>
            </form>
        </section>
    </main>
@endsection
