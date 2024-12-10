@extends('manage.layouts.admin')

@section('title', 'Create IP Ban')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.ip-bans.store') }}" method="post">
                @csrf

                @include('manage.pages.ip-bans._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
