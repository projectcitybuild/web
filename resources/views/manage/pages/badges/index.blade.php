@extends('manage.layouts.admin')

@section('title', 'Badges')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.badges.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Display Name</th>
                <th>Unicode Icon</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($badges as $badge)
                <tr>
                    <td>{{ $badge->getKey() }}</td>
                    <td>{{ $badge->display_name }}</td>
                    <td>{{ $badge->unicode_icon }}</td>
                    <td><a href="{{ route('manage.badges.edit', $badge->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $badges->links('vendor.pagination.bootstrap-4') }}
@endsection
