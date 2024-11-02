@extends('admin.layouts.admin')

@section('title', 'Showcase Warps')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.showcase-warps.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Warp Name</th>
                <th>Title</th>
                <th>Creators</th>
                <th>World</th>
                <th>X,Y,Z</th>
                <th>Direction</th>
                <th>Built At</th>
                <th>Created At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($warps as $warp)
                <tr>
                    <td>{{ $warp->getKey() }}</td>
                    <td><strong>{{ $warp->name }}</strong></td>
                    <td>{{ $warp->title }}</td>
                    <td>{{ $warp->creators }}</td>
                    <td>{{ $warp->location_world }}</td>
                    <td>({{ $warp->location_x }}, {{ $warp->location_y }}, {{ $warp->location_z }})</td>
                    <td>p: {{ $warp->location_pitch }}, y: {{ $warp->location_yaw }}</td>
                    <td>{{ $warp->built_at }}</td>
                    <td>{{ $warp->created_at }}</td>
                    <td><a href="{{ route('manage.showcase-warps.edit', $warp->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $warps->links('vendor.pagination.bootstrap-4') }}
@endsection
