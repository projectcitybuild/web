@extends('v2.front.templates.master')

@section('title', 'Setup 2FA - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page page--narrow settings mfa">
        <form action="{{ route('front.account.security.finish') }}" method="post">
            <div class="settings__content">
                <div class="settings__section">
                    <h2 class="settings__section-heading">Enable 2FA</h2>
                    <div class="settings__section-description">
                        Follow the steps below to enable 2-factor Authentication on your account.
                    </div>
                    @include('v2.front.components.form-error')
                </div>
                <div class="settings__section">
                    <h2>Step 1: Download a 2FA app</h2>
                    <p>You'll need an app on your phone to set up 2-factor authentication, like <a
                            href="https://www.microsoft.com/en-us/account/authenticator">Microsoft
                            Authenticator</a>, or
                        <a
                            href="https://authy.com/download/">Authy</a>.</p>
                    <p>If you use a password manager like 1Password or LastPass, it might support 2FA too.</p>
                </div>
                <div class="settings__section">
                    <h2>Step 2: Save your backup code</h2>
                    <p>You need to save your backup code somewhere safe. If you ever lose access to your
                        authentication
                        app you can use this to disable 2FA on your account.</p>

                    <div class="mfa__backup">
                        <label>Your backup code:</label>
                        <div class="mfa__backup-code">{{ $backupCode }}</div>
                    </div>

                    <p>Please check below to confirm you've saved this:</p>
                    <label class="mfa__backup-confirm checkbox {{ $errors->has('backup_saved') ? 'checkbox--has-error' : '' }}">
                        <input type="checkbox" name="backup_saved" value="1" @if(old('backup_saved', 0) == 1) checked @endif>
                        I've saved this code somewhere safe
                    </label>
                </div>
                <div class="settings__section">
                    <h2>Step 3: Scan the QR Code</h2>

                    <div>
                        <p>Scan this QR Code using your 2FA app:</p>

                        <div class="mfa__keys">
                            <div class="mfa__qr-container">
                                {!! $qrSvg !!}
                            </div>
                            <div class="mfa__qr-manual">
                                If you can't scan the code, manually enter the key
                                <div class="mfa__manual-key">
                                    {{ $secretKey }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings__section">
                    <h2>Step 4: Enter a Code</h2>

                    <p>Enter the current code your 2FA app generates:</p>

                    @csrf

                    <div class="form-row">
                        <label for="code">2FA Code</label>
                        <input class="textfield textfield--is-mfa {{ $errors->any() ? 'input-text--error' : '' }}"
                               maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                               size="6"
                               name="code" id="code" type="text"/>
                    </div>
                </div>

                <div class="settings__section mfa__submit-section">
                    <button class="button button--filled button--has-icon-right" type="submit">
                            Enable <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </main>
@endsection
