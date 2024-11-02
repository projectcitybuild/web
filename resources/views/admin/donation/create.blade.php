@extends('admin.layouts.admin')

@section('title', 'Create Donation')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.donations.store') }}" method="post">
                @csrf

                @include('admin.donation._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
