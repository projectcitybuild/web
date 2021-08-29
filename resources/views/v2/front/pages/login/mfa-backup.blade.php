@extends('v2.front.templates.master')

@section('title', '2FA Recovery')
@section('description', '')

@section('body')
    <main class="page login">
        <section class="login__dialog login__mfa-recover">
            <h1>Recover MFA</h1>

            @if($errors->any())
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <p class="form__description">
                If you've lost access to your 2FA device, you can disable 2FA on your account by entering your backup code.
            </p>

            <form action="{{ route('front.login.mfa-recover') }}" class="form" method="post">
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
