@extends('front.layouts.master')

@section('title', 'Your Donations')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        @if(Session::get('mfa_setup_finished', false))
            <div class="alert alert--success"></div>
        @endif

        <form action="{{ route('front.account.security.finish') }}" method="post">
            <div class="card card--divided card--medium card--centered">
                <div class="card__body card__body--padded">
                    <h1>Enable 2FA</h1>
                    @include('front.components.form-error')
                    <p class="header-description">Follow the steps below to enable 2-factor Authentication on your
                        account</p>
                </div>
                <div class="card__body card__body--padded">
                    <h2>Step 1: Download a 2FA app</h2>
                    <p>You'll need an app on your phone to set up 2-factor authentication, like <a
                            href="https://www.microsoft.com/en-us/account/authenticator">Microsoft Authenticator</a>, or
                        <a
                            href="https://authy.com/download/">Authy</a>.</p>
                    <p>If you use a password manager like 1Password or LastPass, it might support 2FA too.</p>
                </div>
                <div class="card__body card__body--padded">
                    <h2>Step 2: Save your backup code</h2>
                    <p>You need to save your backup code somewhere safe. If you ever lose access to your authentication
                        app you can use this to disable 2fa on your account.</p>


                    <div class="twofa__backup">
                        <label>Your backup code:</label>
                        <div class="twofa__backup-code">{{ $backupCode }}</div>
                    </div>

                    <p>Please check below to confirm you've saved this:</p>
                    <label>
                        <input type="checkbox" name="backup_saved" value="1"
                               @if(old('backup_saved', 0) == 1) checked @endif>
                        I've saved this code somewhere safe</label>
                </div>
                <div class="card__body card__body--padded">
                    <h2>Step 3: Scan the QR Code</h2>

                    <div>
                        <p>Scan this QR Code using your 2FA app:</p>

                        <div class="twofa__keys">
                            <div class="twofa__qr-container">
                                {!! $qrSvg !!}
                            </div>
                            <div class="twofa__qr-manual">
                                If you can't scan the code, manually enter the key
                                <div class="twofa__manual-key">
                                    {{ $secretKey }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card__body card__body--padded">
                    <h2>Step 4: Enter a Code</h2>

                    <p>Enter the current code your 2FA app generates:</p>

                    @csrf

                    <div class="form-row">
                        <label for="code">2FA Code</label>
                        <input class="input-text input-2fa {{ $errors->any() ? 'input-text--error' : '' }}"
                               maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" size="6"
                               name="code" id="code" type="text"/>
                    </div>

                    <button class="button button--primary button--large">Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
