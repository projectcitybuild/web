@extends('v2.front.templates.master')

@section('title', 'Game Accounts - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('v2.front.pages.account.components.account-sidebar')
        <div class="settings__content">
            <div class="settings__section">
                <h2 class="settings__section-heading">Game Accounts</h2>

                @if(Session::get('game_account_added', false))
                    <div class="alert alert--success contents__flash">
                        <h2><i class="fas fa-check"></i> Account Linked</h2>
                        Your Minecraft account has been successfully linked to your PCB account. <br/>
                        Please run the <strong>/sync finish</strong> command in-game to finish the process.
                    </div>
                @endif


                <p class="header-description">The game accounts you've linked to your PCB account.
                    Linked
                    accounts will automatically receive your rank.</p>
            </div>

            @if($mcAccounts->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-gamepad fa-2x"></i></div>
                    <p>You don't have any game accounts.</p>
                </div>
            @else
                <div class="settings__section game-account-grid">
                    @foreach($mcAccounts as $mcAccount)
                        <div class="game-account">
                            <div class="game-account__avatar" >
                                <img src="https://minotar.net/avatar/{{ $mcAccount->uuid }}/64"  alt="">
                            </div>
                            <div class="game-account__details">
                                <div class="game-account__game">
                                    Minecraft
                                     &middot;
                                    @isset($mcAccount->last_synced_at)
                                        <span>Seen {{ $mcAccount->last_synced_at->diffForHumans() }}</span>
                                    @else
                                        <span>Never seen</span>
                                    @endif
                                </div>
                                <div class="game-account__alias">
                                    @if($mcAccount->aliases()->count() == 0)
                                        <em>No alias</em>
                                    @else
                                        {{ $mcAccount->aliases->last()->alias }}
                                    @endempty
                                </div>
                                <div class="game-account__id">{{ $mcAccount->uuid }}</div>
                                <div class="game-account__actions">
                                    <form action="{{ route('front.account.games.delete', $mcAccount) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button button--secondary button--filled button--is-small">Unlink</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="settings__section">
                <h2 class="settings__section-heading">Add new</h2>

                <p class="settings__description">To add a new Minecraft account, <strong>run /sync</strong> in-game.</p>
            </div>
        </div>
    </main>
@endsection
