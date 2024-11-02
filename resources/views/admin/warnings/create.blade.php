@extends('admin.layouts.admin')

@section('title', 'Create Warning')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.warnings.store') }}" method="post">
                @csrf

                @include('admin.warnings._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
