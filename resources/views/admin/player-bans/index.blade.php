@extends('admin.layouts.admin')

@section('title', 'Player Bans')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.panel.player-bans.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Active</th>
                <th>Player</th>
                <th>Reason</th>
                <th>Banned By</th>
                <th>Expires At</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Unbanned At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($bans as $ban)
                <tr>
                    <td>{{ $ban->getKey() }}</td>
                    <td>
                        <i class="fas {{ $ban->isActive() ? 'fa-check' : 'fa-multiply' }} fa-fw"></i>
                    </td>
                    <td>
                        <a href="{{ route('front.panel.minecraft-players.show', $ban->bannedPlayer) }}">
                            {{ $ban->bannedPlayer->getBanReadableName() ?: '(No Alias)' }}
                        </a>
                    </td>
                    <td>{{ $ban->reason }}</td>
                    <td>
                        @if ($ban->staffPlayer)
                        <a href="{{ route('front.panel.minecraft-players.show', $ban->staffPlayer) }}">
                            {{ $ban->staffPlayer?->getBanReadableName() ?: '(No Alias)' }}
                        </a>
                        @else
                            System
                        @endif
                    </td>
                    <td>{{ $ban->expires_at ?? '-' }}</td>
                    <td>{{ $ban->created_at }}</td>
                    <td>{{ $ban->updated_at }}</td>
                    <td>{{ $ban->unbanned_at ?? '-' }}</td>
                    <td>
                        <a href="{{ route('front.panel.player-bans.edit', $ban) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $bans->links('vendor.pagination.bootstrap-4') }}
@endsection
