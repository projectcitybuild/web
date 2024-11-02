@extends('manage.layouts.admin')

@section('title', 'Server Tokens')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.server-tokens.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Assigned Server</th>
                <th>Token</th>
                <th>Description</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($tokens as $token)
                <tr>
                    <td>{{ $token->getKey() }}</td>
                    <td>
                        <a href="{{ route('manage.servers.edit', $token->server->getKey()) }}">
                            {{ $token->server->name ?? '(Unset)' }}
                        </a>
                    </td>
                    <td>{{ $token->token }}</td>
                    <td>{{ $token->description ?: '-' }}</td>
                    <td><a href="{{ route('manage.server-tokens.edit', $token->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $tokens->links('vendor.pagination.bootstrap-4') }}
@endsection
