@extends('admin.layouts.admin')

@section('title', 'Create Server Token')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.server-tokens.store') }}" method="post">
                @csrf

                @include('admin.server-tokens._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
