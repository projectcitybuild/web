@extends('front.layouts.master')

@section('title', 'Account Links')
@section('description', '')

@section('contents')

    <div class="contents__body">

        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Connected Social Accounts</h1>
                <span class="header-description">Linked social accounts for logging-in</span>
            </div>

            <div class="card__body card__body--padded">
                @if(Session::has('success'))
                    <div class="alert alert--success">
                        <h3><i class="fas fa-exclamation-circle"></i> Success</h3>
                        {{ Session::get('success') }}
                    </div>
                    <p>
                @endif

                <div class="social-accounts">

                    <div class="social-accounts__item {{ isset($links['facebook']) ? 'social-accounts__item--registered' : '' }}">
                        <h3>Facebook</h3>
                        <i class="social-icon fab fa-facebook fa-4x"></i>

                        @isset($links['facebook'])
                            <a class="button button--fill" href="{{ route('front.account.social.delete', 'facebook') }}">Remove</a>
                        @else
                            <a class="button button--fill button--secondary" href="{{ route('front.account.social.redirect', 'facebook') }}">Connect</a>
                        @endif
                    </div>

                    <div class="social-accounts__item {{ isset($links['twitter']) ? 'social-accounts__item--registered' : '' }}">
                        <h3>Twitter</h3>
                        <i class="social-icon fab fa-twitter fa-4x"></i>

                        @isset($links['twitter'])
                            <a class="button button--fill" href="{{ route('front.account.social.delete', 'twitter') }}">Remove</a>
                        @else
                            <a class="button button--fill button--secondary" href="{{ route('front.account.social.redirect', 'twitter') }}">Connect</a>
                        @endif
                    </div>

                    <div class="social-accounts__item {{ isset($links['google']) ? 'social-accounts__item--registered' : '' }}">
                        <h3>Google</h3>
                        <i class="social-icon fab fa-google fa-4x"></i>

                        @isset($links['google'])
                            <a class="button button--fill" href="{{ route('front.account.social.delete', 'google') }}">Remove</a>
                        @else
                            <a class="button button--fill button--secondary" href="{{ route('front.account.social.redirect', 'google') }}">Connect</a>
                        @endif
                    </div>
                    
                </div>
            </div>

        </div>

    </div>

    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>


@endsection