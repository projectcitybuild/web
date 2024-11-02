@extends('manage.layouts.admin')

@section('title', 'Create Server')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.servers.store') }}" method="post">
                @csrf

                @include('manage.pages.servers._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
