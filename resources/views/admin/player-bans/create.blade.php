@extends('admin.layouts.admin')

@section('title', 'Create Player Ban')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.player-bans.store') }}" method="post">
                @csrf

                @include('admin.player-bans._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
