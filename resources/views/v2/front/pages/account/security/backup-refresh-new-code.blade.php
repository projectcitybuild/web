@extends('front.layouts.master')

@section('title', 'Refresh 2FA Backup')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        <div class="card card--divided card--medium card--centered">
            <div class="card__body card__body--padded">
                <h1>Refresh Backup Code</h1>
            </div>
            <div class="card__body card__body--padded">
                <div class="alert alert--error">You must keep this code in a safe place. It is the only way to regain
                    access to your account if you lose your 2FA device.
                </div>
                <div class="twofa__backup">
                    <label>Your new backup code:</label>
                    <div class="twofa__backup-code">{{ $backupCode }}</div>
                </div>
            </div>
            <div class="card__footer">
                <div class="twofa__buttons">
                    <a href="{{ route('front.account.security') }}" class="button button--primary">Finish <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection
