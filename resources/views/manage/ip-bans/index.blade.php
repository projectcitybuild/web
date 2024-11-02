@extends('manage.layouts.admin')

@section('title', 'IP Bans')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.ip-bans.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>Reason</th>
                <th>Banned By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($bans as $ban)
                <tr>
                    <td>{{ $ban->getKey() }}</td>
                    <td>{{ $ban->ip_address }}</td>
                    <td>{{ $ban->reason }}</td>
                    <td>
                        <a href="{{ route('manage.minecraft-players.show', $ban->bannerPlayer) }}">
                            {{ $ban->bannerPlayer->alias ?: '(No Alias)' }}
                        </a>
                    </td>
                    <td>{{ $ban->created_at }}</td>
                    <td>{{ $ban->updated_at }}</td>
                    <td>
                        <a href="{{ route('manage.ip-bans.edit', $ban) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $bans->links('vendor.pagination.bootstrap-4') }}
@endsection
