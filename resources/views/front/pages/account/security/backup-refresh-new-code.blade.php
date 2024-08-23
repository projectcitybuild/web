@extends('front.templates.master')

@section('title', 'Refresh 2FA Backup')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page page--narrow settings">
        <div class="settings__content">
            <div class="settings__section">
                <h2 class="settings__section-heading">Refresh Backup Code</h2>
                <div class="alert alert--error">
                    You <strong>must</strong> store this code. It is the only way to regain access to your account if you lose your 2FA device.
                </div>
                <div class="mfa__backup">
                    <label>Your new backup code:</label>
                    <div class="mfa__backup-code">{{ $backupCode }}</div>
                </div>
                <div class="toolbar">
                    <a href="{{ route('front.account.security') }}" class="button button--filled button--has-icon-right">Finish <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>

        </div>
    </main>

    @include('front.components.footer')
@endsection

