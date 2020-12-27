@extends('front.layouts.master')

@section('title', 'Disable 2FA')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        <div class="card card--divided card--medium card--centered">
            <div class="card__body card__body--padded">
                <h1>Disable 2FA</h1>
            </div>
            <div class="card__body card__body--padded">
                <p>Are you sure you want to disable 2FA?</p>
                <p>You will no longer need this device to authenticate. You can re-enable 2FA at any time, but
                    you'll be given a different code and backup code.</p>

                <form action="{{ route('front.account.security.disable') }}" method="post">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="card__footer">
                <div class="twofa__buttons">
                    <a href="{{ route('front.account.security') }}" class="button button--large button--accent">Cancel</a>
                    <button type="submit" class="button button--primary button--large">
                        Confirm <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
