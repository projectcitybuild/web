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
                <p>Are you sure you want to refresh your 2FA backup code?</p>
                <p>The old backup code will stop working and you'll need to safely store the new one.</p>


            </div>
            <div class="card__footer">
                <form action="{{ route('front.account.security.reset-backup') }}" method="post">
                    @csrf
                    <div class="twofa__buttons">
                        <a href="{{ route('front.account.security') }}" class="button button--large button--accent">Cancel</a>
                        <button type="submit" class="button button--primary button--large">
                            Confirm <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
