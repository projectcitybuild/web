@extends('admin.layouts.admin')

@section('title', 'Create IP Ban')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.ip-bans.store') }}" method="post">
                @csrf

                @include('admin.ip-bans._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
