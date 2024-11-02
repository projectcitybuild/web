@extends('admin.layouts.admin')

@section('title', 'Warps')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.minecraft.warps.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Warp Name</th>
                <th>World</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>Pitch</th>
                <th>Yaw</th>
                <th>Created At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($warps as $warp)
                <tr>
                    <td>{{ $warp->getKey() }}</td>
                    <td><strong>{{ $warp->name }}</strong></td>
                    <td>{{ $warp->world }}</td>
                    <td>{{ $warp->x }}</td>
                    <td>{{ $warp->y }}</td>
                    <td>{{ $warp->z }}</td>
                    <td>{{ $warp->pitch }}</td>
                    <td>{{ $warp->yaw }}</td>
                    <td>{{ $warp->created_at }}</td>
                    <td><a href="{{ route('manage.minecraft.warps.edit', $warp->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $warps->links('vendor.pagination.bootstrap-4') }}
@endsection
