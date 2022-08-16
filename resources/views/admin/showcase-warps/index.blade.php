@extends('admin.layouts.admin')

@section('title', 'Showcase Warps')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.panel.showcase-warps.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
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
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($warps as $warp)
                <tr>
                    <td>{{ $warp->getKey() }}</td>
                    <td>{{ $warp->name }}</td>
                    <td>{{ $warp->title }}</td>
                    <td>{{ $warp->creators }}</td>
                    <td><a href="{{ route('front.panel.showcase-warps.edit', $warp->getKey()) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $warps->links('vendor.pagination.bootstrap-4') }}
@endsection
