@extends('front.templates.master')

@section('title', 'Change Email - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        <div class="settings__content">
            <div class="settings__section">
                <h2 class="settings__section-heading">Confirm Email Change</h2>
                <span class="form__description">
                    You have requested to change your email address.<br/>
                    Please click the link sent to both email addresses to complete the process.
                </span>

                <div class="change-status">
                    <div class="change-status__item">
                        <div class="change-status__icon"><i class="fas fa-fw {{ $changeRequest->is_previous_confirmed ? 'fa-check' : 'fa-hourglass-half' }}"></i></div>
                        <div class="change-status__name">{{ $changeRequest->email_previous }}</div>
                        <div class="change-status__description">Current email address</div>
                    </div>

                    <div class="change-status__item">
                        <div class="change-status__icon"><i class="fas fa-fw {{ $changeRequest->is_new_confirmed ? 'fa-check' : 'fa-hourglass-half' }}"></i></div>
                        <div class="change-status__name">{{ $changeRequest->email_new }}</div>
                        <div class="change-status__description">New email address</div>
                    </div>
                </div>

            </div>
        </div>

    </main>
@endsection
