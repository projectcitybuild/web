@extends('manage.layouts.admin')

@section('title', 'Create Showcase Warp')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.showcase-warps.store') }}" method="post">
                @csrf

                @include('manage.showcase-warps._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
