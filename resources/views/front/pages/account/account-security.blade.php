@extends('front.layouts.master')

@section('title', 'Security Settings')
@section('description', '')

@section('contents')
    <div class="contents__body">
        @if(Session::get('mfa_setup_required', false))
            <div class="alert alert--error contents__flash">
                <h3><i class="fas fa-exclamation-circle"></i> 2FA Setup Required</h3>
                <p>
                    You need to set up 2FA to use {{ Session::get('mfa_setup_required_feature', 'this feature') }}.
                </p>
            </div>
        @endif
        @if(Session::get('mfa_setup_finished', false))
            <div class="alert alert--success contents__flash">
                <h3><i class="fas fa-check"></i> 2FA Enabled</h3>
                <p>
                    2FA has successfully been enabled on your account
                </p>
            </div>
        @endif
        @if(Session::get('mfa_disabled', false))
            <div class="alert alert--success contents__flash">
                <h3><i class="fas fa-check"></i> 2FA Disabled</h3>
                <p>
                    2FA has successfully removed from your account
                </p>
            </div>
        @endif
        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Security</h1>
                <span class="header-description">Manage your 2-factor authentication settings</span>
            </div>

            @if(Auth::user()->is_totp_enabled)
                <div class="card__body card__body--padded">
                    <h2 class="account-security__twofa-enabled"><i class="fas fa-lock"></i> 2FA Enabled</h2>
                    <p>You've secured your account with 2-factor authentication.</p>

                    <a href="{{ route('front.account.security.reset-backup') }}" class="button button--primary">Regenerate Backup Code</a>
                    <a href="{{ route('front.account.security.disable') }}" class="button button--primary">Disable 2FA</a>
                </div>
            @else
                <div class="card__body card__body--padded">
                    <h2 class="account-security__twofa-not-enabled"><i class="fas fa-unlock"></i> 2FA Not Enabled</h2>
                    <p>You don't have 2FA enabled yet. It's optional, but it will help protect your account.</p>

                    <form action="{{ route('front.account.security.start') }}" method="post">
                        @csrf
                        <button class="button button--large button--primary">
                            Set Up
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>
@endsection
