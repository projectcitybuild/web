@extends('admin.layouts.admin')

@section('title', 'Create Server')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.servers.store') }}" method="post">
                @csrf

                @include('admin.servers._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
