@extends('admin.layouts.admin')

@section('title', 'Create Badge')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.badges.store') }}" method="post">
                @csrf

                @include('admin.badges._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
