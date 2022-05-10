@extends('admin.layouts.admin')

@section('title', 'Edit Page #' . $page->page_id)

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('front.page', $page->url) }}" class="btn btn-outline-primary" target="_blank"><i class="fas fa-eye"></i> Preview</a>
        <a href="{{ route('front.panel.pages.create') }}" class="btn btn-outline-danger" target="_blank"><i class="fas fa-trash"></i> Delete</a>
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
