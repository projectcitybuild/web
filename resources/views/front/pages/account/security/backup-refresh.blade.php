@extends('front.templates.master')

@section('title', 'Refresh 2FA Backup Code - Account Settings - Project City Build')
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
                <p class="form__description">Are you sure you want to refresh your 2FA backup code?</p>
                <p class="form__description">The old backup code will stop working and you'll need to safely store the
                    new one.</p>
                <form action="{{ route('front.account.security.reset-backup.confirm') }}" method="post" class="toolbar">
                    @csrf
                    <a href="{{ route('front.account.security') }}"
                       class="button button--filled button--secondary">Cancel</a>
                    <button type="submit" class="button button--filled button--has-icon-right">
                        Confirm <i class="fas fa-chevron-right"></i>
                    </button>
                </form>
            </div>

        </div>
    </main>

    @include('front.components.footer')
@endsection
