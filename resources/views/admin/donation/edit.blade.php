@extends('admin.layouts.admin')

@section('title', 'Edit Donation #' . $donation->donation_id)

@section('body')
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('front.panel.donations.update', $donation) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.donation._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
