@extends('manage.layouts.admin')

@section('title', 'Create Warning')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.warnings.store') }}" method="post">
                @csrf

                @include('manage.warnings._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
