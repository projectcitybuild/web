@extends('front.layouts.master')

@section('title', 'Game Accounts')
@section('description', '')

@section('contents')
    <div class="contents__body account-donations">
        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Game Accounts</h1>
                <span class="header-description">The Minecraft accounts you've linked to your PCB account. Linked accounts will automatically receive your rank.</span>
                <p class="header-description">To link a new account, run /sync in game and follow the instructions</p>
            </div>
            <div class="card__body card--no-padding">
                <table class="table table--striped table--first-col-padded">
                    <thead>
                    <tr>
                        <th>UUID</th>
                        <th>Last Alias</th>
                        <th>Last Seen</th>
                        <th>Linked</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mcAccounts as $mcAccount)
                        <tr>
                            <td>
                                <img src="https://minotar.net/avatar/{{ $mcAccount->uuid }}/16" alt="">
                                {{ $mcAccount->uuid }}
                            </td>
                            <td>
                                @if($mcAccount->aliases()->count() == 0)
                                    <em>No alias</em>
                                @else
                                    {{ $mcAccount->aliases->last()->alias }}
                                @endempty
                            </td>
                            <td>{{ $mcAccount->last_seen_at->toFormattedDateString() }}</td>
                            <td>{{ $mcAccount->created_at->toFormattedDateString() }}</td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>
@endsection
