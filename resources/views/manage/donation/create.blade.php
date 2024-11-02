@extends('manage.layouts.admin')

@section('title', 'Create Donation')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.donations.store') }}" method="post">
                @csrf

                @include('manage.donation._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
