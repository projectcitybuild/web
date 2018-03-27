@extends('layouts.master')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@section('contents')

    <div class="card">
        <div class="card__body">
            <h1>Set Your Password</h1>

            <p>
                Please enter a new password for your account.
            </p>

            <hr>

            <form method="post" action="{{ route('front.password-reset.save') }}">
                @csrf
                
                @if($errors->any())
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        {{ $errors->first() }}
                    </div>
                    <p>
                @endif

                <input type="hidden" name="password_token" value="{{ $passwordToken }}" />

                <div class="form-row">
                    <label>Password</label>
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Password" />
                </div>

                <div class="form-row">
                    <label>Password Confirm</label>
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password_confirm" type="password" placeholder="Password" />
                </div>

                <div>
                    <button class="button button--large button--fill button--primary" type="submit">
                        <i class="fas fa-check"></i> Save Password
                    </button>
                </div>

            </form>
        </div>

    </div>

@endsection