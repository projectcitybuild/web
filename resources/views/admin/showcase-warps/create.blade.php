@extends('admin.layouts.admin')

@section('title', 'Create Showcase Warp')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.showcase-warps.store') }}" method="post">
                @csrf

                @include('admin.showcase-warps._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
