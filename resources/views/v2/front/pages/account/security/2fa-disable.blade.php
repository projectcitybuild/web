@extends('v2.front.templates.master')

@section('title', 'Disable 2FA - Account Settings - Project City Build')
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
                <h2 class="settings__section-heading">Disable 2FA</h2>
                <p class="settings__description">Are you sure you want to disable 2FA?</p>
                <p class="settings__description">You will no longer need this device to authenticate. You can re-enable 2FA at any time, but
                    you'll be given a different code and backup code.</p>

                <form action="{{ route('front.account.security.disable') }}" method="post" class="toolbar">
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('front.account.security') }}" class="button button--filled button--secondary">Cancel</a>
                    <button type="submit" class="button button--filled button--has-icon-right">
                        Confirm <i class="fas fa-chevron-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection
