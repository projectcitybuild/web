@extends('admin.layouts.admin')

@section('title', 'Create Page')

@section('body')
    <div class="row">
        <div class="col-md-12">
            @include('admin._errors')
            <form action="{{ route('front.panel.pages.store') }}" method="post">
                @csrf

                @include('admin.page._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
