@extends('admin.layouts.admin')

@section('title', 'Servers')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.panel.servers.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>IP address</th>
                <th>Port</th>
                <th>IP alias</th>
                <th>Game type</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($servers as $server)
                <tr>
                    <td>{{ $server->getKey() }}</td>
                    <td>{{ $server->name }}</td>
                    <td>{{ $server->ip }}</td>
                    <td>{{ $server->port }}</td>
                    <td>{{ $server->ip_alias ?: '' }}</td>
                    <td>{{ \Entities\Models\GameType::tryFrom($server->game_type)?->name() ?: 'Unknown' }}</td>
                    <td><a href="{{ route('front.panel.servers.edit', $server->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $servers->links('vendor.pagination.bootstrap-4') }}
@endsection
