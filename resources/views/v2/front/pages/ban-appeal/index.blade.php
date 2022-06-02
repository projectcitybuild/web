@extends('v2.front.pages.ban-appeal._layout-form')

@section('col-2')
    @auth
        <div class="contents__section">
            <h2>Current Bans</h2>
            <div class="game-ban-column">
                @each('v2.front.pages.ban-appeal._game-ban-listing', $bans, 'ban')
            </div>
        </div>
        <div class="contents__section">
            <h2>Look up Account</h2>
            <p>If the ban doesn't appear above, try searching manually. Enter your <strong>current</strong> Minecraft
                username.</p>

            @include('v2.front.pages.ban-appeal._username-lookup')
        </div>
    @else
        <div class="contents__section">
            <h2>Sign in to Appeal</h2>
            <p>To appeal a ban, sign in to your PCB account. Alternatively, you may appeal as a guest.</p>
            <a href="{{ route('front.appeal.auth') }}" class="button button--filled">Sign In </a>
        </div>
        <div class="contents__section">
            <h2>Appeal as a Guest</h2>
            <p>To start your appeal, enter the <strong>current</strong> username of your Minecraft account</p>

            @include('v2.front.pages.ban-appeal._username-lookup')
        </div>
    @endauth
@endsection
