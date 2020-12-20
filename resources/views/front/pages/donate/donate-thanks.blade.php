@extends('front.layouts.master')

@section('title', 'Thank you for your donation')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Thank you for your donation!</h1>

            <div class="donate__confirmation--info">
                Your perks have been assigned to your account if you were logged into the website.<br />
                If you are currently in-game, please reconnect to receive your perks.
                <p />
                In the case where you don't immediately receive your perks, please wait a few minutes as your payment may still
                be processing.
            </div>

            <div class="donate__confirmation--info">
                If you have any problems, please do not hesitate to reach out to staff on the forums, Discord or in-game.
            </div>

            <a class="button button--secondary" href="{{ route('front.account.donations') }}">
                See your donations <i class="fas fa-chevron-right"></i>
            </a>

            <a class="button button--secondary" href="{{ route('front.home') }}">
                Back to Home <i class="fas fa-chevron-right"></i>
            </a>
        </div>

    </div>

@endsection
