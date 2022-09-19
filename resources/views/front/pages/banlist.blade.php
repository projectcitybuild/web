@extends('front.templates.master')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Ban List</h1>
        </div>
    </header>

    <main class="page settings">
        <div class="settings__section">
            <table class="table table--divided">
                <thead>
                <tr>
                    <th colspan="5">
                        <form action="{{ route('front.banlist') }}" method="get">
                            <input type="text" class="input-text" name="query" value="{{ $query }}" placeholder="Search banned player or reason">
                        </form>
                    </th>
                </tr>
                <tr>
                    <th>Banned Player</th>
                    <th>Reason</th>
                    <th>Banned By</th>
                    <th>Banned At</th>
                    <th>Expires</th>
                </tr>
                </thead>
                <tbody>
                @forelse($bans as $ban)
                    <tr>
                        <td>
                            <img src="https://minotar.net/avatar//{{ $ban->bannedPlayer->uuid }}/16" class="banlist__head" alt="">
                            {{ $ban->banned_alias_at_time }}
                        </td>
                        <td>
                            @if ($ban->reason != null)
                                {{ $ban->reason }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($ban->banner_player_id != null && count($ban->bannerPlayer->aliases) > 0)
                                <img src="https://minotar.net/avatar/{{ $ban->bannerPlayer->uuid }}/16" class="banlist__head" alt="">
                                {{ $ban->bannerPlayer->getBanReadableName() }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $ban->created_at->format('j M Y H:i') }}
                        </td>
                        <td>
                            @if ($ban->expires_at != null)
                                {{ $ban->created_at->format('j M Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No bans match your search criteria</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $bans->links('vendor.pagination.default') }}
        </div>
    </main>
@endsection
