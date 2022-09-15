@extends('admin.layouts.admin')

@section('title', 'Ban Appeals')

@section('body')
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Status</th>
                <th>Player Name</th>
                <th>Banning Staff</th>
                <th>Appealed At</th>
                <th>Decided At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($banAppeals as $banAppeal)
                <tr class="{{ $banAppeal->isPending()  ? 'table-warning' : '' }}">
                    <td>{{ $banAppeal->status->humanReadable() }}</td>
                    <td>
                        <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gamePlayerBan->bannedPlayer)}}">
                            {{ $banAppeal->getBannedPlayerName() ?? 'No Alias' }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gamePlayerBan->bannedPlayer)}}">
                            {{ $banAppeal->gamePlayerBan->staffPlayer->getBanReadableName() ?? 'No Alias' }}
                        </a>
                    </td>
                    <td>
                        {{ $banAppeal->created_at }}
                    </td>
                    <td>
                        {{ $banAppeal->decided_at ?? '-' }}
                    </td>
                    <td>
                        <a href="{{ route('front.panel.ban-appeals.show', $banAppeal) }}">View</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $banAppeals->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection
