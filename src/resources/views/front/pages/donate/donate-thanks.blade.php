@extends('front.layouts.master')

@section('title', 'Account Verification')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Thank you for your donation!</h1>

            <div class="donate__confirmation--info">
                Your perks have been assigned to your account if you were logged into the website.<br />
                If you are currently in-game, please reconnect to receive your perks.
            </div>
            
            <div class="donate__confirmation">
                <div class="donate__confirmation__left">
                    <i class="fas fa-gift fa-4x"></i>
                </div>
                <div class="donate__confirmation__right">
                    <h3>Payment Details</h3>

                    Amount donated 
                    <div class="donate__confirmation--amount">${{ number_format($donation['amount'], 2) }}</div>

                    @auth
                        @if($donation['is_lifetime_perks'] == false)
                            Donator perks until: 
                            <div class="donate__confirmation--expiry">{{ date('M jS (D) Y, g:iA', $donation['perks_end_at']->timestamp) }}</div>
                        @else
                            Donator perks until: 
                            <div class="donate__confirmation--expiry">Forever</div>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="donate__confirmation--info">
                If you have any problems, please do not hesitate to reach out to staff on the forums, Discord or in-game.
            </div>

            <p />

            <a class="button button--secondary" href="{{ route('front.home') }}">
                Back to Home <i class="fas fa-chevron-right"></i>
            </a>
        </div>

    </div>

@endsection