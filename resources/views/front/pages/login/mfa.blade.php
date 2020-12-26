@extends('front.layouts.master')

@section('title', 'Re-enter Password')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        <form action="{{ route('front.login.mfa') }}" method="post">
            <div class="card card--divided card--narrow card--centered">
                <div class="card__body card__body--padded">
                    <h1>2FA Confirmation</h1>
                    @csrf
                    @include('front.components.form-error')
                    <div class="form-row">
                        <label for="code">Enter your current 2FA code to continue</label>
                        <input class="input-text input-2fa {{ $errors->any() ? 'input-text--error' : '' }}"
                               maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" size="6"
                               name="code" id="code" type="text"/>
                    </div>
                </div>
                <div class="card__footer">
                    <button type="submit" class="button button--primary button--large button--fill">Verify <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </form>
    </div>
@endsection
