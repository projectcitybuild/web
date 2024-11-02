@extends('manage.layouts.admin')

@section('title', 'Create Badge')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.badges.store') }}" method="post">
                @csrf

                @include('manage.pages.badges._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
