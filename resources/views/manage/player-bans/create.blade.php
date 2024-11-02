@extends('manage.layouts.admin')

@section('title', 'Create Player Ban')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.player-bans.store') }}" method="post">
                @csrf

                @include('manage.player-bans._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
