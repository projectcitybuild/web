@extends('front.layouts.master')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('contents')
    <div id="banlist">
        <h1>Ban List</h1>
        <table class="table table--divided">
            <thead>
            <tr>
                <th>
                    Banned Player
                </th>
                <th>
                    Reason
                </th>
                <th>
                    Banned By
                </th>
                <th>
                    Banned At
                </th>
                <th>
                    Expires
                </th>
            </tr>
            <tr>
                <th colspan="5">
                    <form action="{{ route('front.banlist') }}" method="get">
                        <input type="text" class="input-text" name="search" placeholder="Search banned player, reason or staff member">
                    </form>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($bans as $ban)
                <tr>
                    <td>
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
                        {{ $ban->staff_player_id }}
                    </td>
                    <td>
                        {{ $ban->created_at->diffForHumans() }}
                    </td>
                    <td>
                        @if ($ban->expires_at != null)
                        {{ $ban->expires_at->diffForHumans() }}
                        @else
                        -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

        {{ $bans->links('vendor.pagination.default') }}

    </div>
@endsection
