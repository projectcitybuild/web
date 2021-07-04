@extends('admin.layouts.admin')

@section('title', 'Minecraft Players')

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Last Alias</th>
                <th>UUID</th>
                <th>Owner</th>
                <th>Manage</th>
            </tr>
            </thead>
            <tbody>
                @foreach($minecraftPlayers as $player)
                    <tr>
                        <td>{{ $player->player_minecraft_id }}</td>
                        <td>{{ $player->getBanReadableName() ?: '-' }}</td>
                        <td class="font-monospace">{{ $player->uuid }}</td>
                        <td>
                            @if($player->account)
                                <a href="{{ route('front.panel.accounts.show', $player->account) }}">
                                    {{ $player->account->username ?: '(Unset)' }}
                                </a>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <a href="#">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $accounts->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection
