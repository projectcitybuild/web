@extends('front.layouts.master')

@section('title', 'Security Settings')
@section('description', '')

@section('contents')
    <div class="contents__body">
        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Security</h1>
                <span class="header-description">Manage your 2-factor authentication settings</span>
            </div>
            <div class="card__body card__body--padded">
                <p>You don't have 2FA enabled yet. It's optional, but it will help protect your account.</p>
                <a class="button button--large button--primary" href="{{ route('front.sso.discourse') }}">
                    Enable
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>
@endsection
