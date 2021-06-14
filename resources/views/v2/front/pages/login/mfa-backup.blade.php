@extends('front.layouts.master')

@section('title', '2FA Recovery')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        <form action="{{ route('front.login.mfa-recover') }}" method="post">
            <div class="card card--divided card--narrow card--centered">
                <div class="card__body card__body--padded">
                    <h1>Enter Backup Code</h1>
                    <span class="header-description">
                        If you've lost access to your 2FA device, you can disable 2FA on your account by entering your backup code.
                    </span>
                </div>
                <div class="card__body card__body--padded">
                    @csrf
                    @method('DELETE')
                    @include('front.components.form-error')
                    <div class="form-row">
                        <label for="backup_code">Enter backup code</label>
                        <input type="text" class="input-text" name="backup_code" id="backup_code">
                    </div>
                </div>
                <div class="card__footer">
                    <button type="submit" class="button button--primary button--large button--fill">
                        Continue <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
