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
                <div>
                   <a href="{{ route('front.account.social.redirect', 'facebook') }}">Facebook</a>
                </div>
                <div>
                   <a href="{{ route('front.account.social.redirect', 'twitter') }}">Twitter</a>
                </div>
                <div>
                   <a href="{{ route('front.account.social.redirect', 'google') }}">Google</a>
                </div>
                <div>
                   <a href="{{ route('front.account.social.redirect', 'discord') }}">Discord</a>
                </div>
            </div>

        </div>

    </div>

    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>


@endsection