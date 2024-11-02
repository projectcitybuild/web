@extends('manage.layouts.admin')

@section('title', 'Edit Donation #' . $donation->donation_id)

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.donations.update', $donation) }}" method="post">
                @csrf
                @method('PUT')

                @include('manage.donation._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
