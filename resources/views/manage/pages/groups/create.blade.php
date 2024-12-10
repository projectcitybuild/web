@extends('manage.layouts.admin')

@section('title', 'Create Group')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.groups.store') }}" method="post">
                @csrf

                @include('manage.pages.groups._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
