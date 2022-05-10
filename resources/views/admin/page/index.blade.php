@extends('admin.layouts.admin')

@section('title', 'Pages')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.panel.pages.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Is Draft?</th>
                <th>Created At</th>
                <th>Last Updated At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><x-bs.fa-boolean :data="$page->is_draft" false-class="text-muted" /></td>
                    <td>{{ $page->created_at }}</td>
                    <td>{{ $page->updated_at }}</td>
                    <td><a href="{{ route('front.panel.pages.edit', $page->page_id) }}">Manage</a> | <a href="{{ route('front.page', $page->url) }}">Preview</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
