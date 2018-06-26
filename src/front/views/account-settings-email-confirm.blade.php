@extends('layouts.master')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Confirm Email Change</h1>
            <span class="header-description">
                You have requested to change your email address to {{ $newEmail }}.
                Please verify by entering your password to complete the process.
            </span>

            <form method="post" action="{{ route('front.account.settings.email.confirm.save') }}" id="form">
                @csrf
                @if($errors->any())
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        {{ $errors->first() }}
                    </div>
                    <p>
                @endif

                <input type="hidden" name="old_email" value="{{ $oldEmail }}" />
                <input type="hidden" name="new_email" value="{{ $newEmail }}" />

                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Current Password" />
                </div>

                <div class="form-row">
                    <button type="submit" class="g-recaptcha button button--large button--fill button--secondary">
                        <i class="fas fa-check"></i> Change Email Address
                    </button>
                </div>
            </form>

        </div>
    </div>


@endsection