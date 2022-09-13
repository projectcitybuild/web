@extends('admin.layouts.admin')

@section('title', 'Warnings')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.panel.warnings.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Player</th>
                <th>Reason</th>
                <th>Warned By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($warnings as $warning)
                <tr>
                    <td>{{ $warning->getKey() }}</td>
                    <td>
                        <a href="{{ route('front.panel.minecraft-players.show', $warning->warnedPlayer) }}">
                            {{ $warning->warnedPlayer->getBanReadableName() ?: '(No Alias)' }}
                        </a>
                    </td>
                    <td>{{ $warning->reason }}</td>
                    <td>
                        <a href="{{ route('front.panel.minecraft-players.show', $warning->warnerPlayer) }}">
                            {{ $warning->warnerPlayer->getBanReadableName() ?: '(No Alias)' }}
                        </a>
                    </td>
                    <td>{{ $warning->created_at }}</td>
                    <td>{{ $warning->updated_at }}</td>
                    <td><a href="{{ route('front.panel.warnings.edit', $warning->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $warnings->links('vendor.pagination.bootstrap-4') }}
@endsection
