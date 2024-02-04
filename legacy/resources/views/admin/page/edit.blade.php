@extends('admin.layouts.admin')

@section('title', 'Edit Page: ' . $page->title)

@section('toolbar')
    <div class="btn-toolbar">
        <div class="btn-group me-2" role="group">
            <a href="{{ route('front.page', $page->url) }}" class="btn btn-outline-primary" target="_blank"><i class="fas fa-eye"></i> Preview</a>
        </div>

        <div class="btn-group me-2" role="group">
            <form method="post" action="{{ route('front.panel.pages.destroy', $page) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-12">
            @include('admin._success')
            @include('admin._errors')
            <form action="{{ route('front.panel.pages.update', $page) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.page._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
