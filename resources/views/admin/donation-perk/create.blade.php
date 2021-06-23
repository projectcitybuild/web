@extends('admin.layouts.admin')

@section('title', 'Create Donation Perk')

@section('body')
    <div class="row">
        <div class="col-md-8">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> Users must be manually added to the Donator group once a perk has been created.
            </div>

            <form action="{{ route('front.panel.donation-perks.store') }}" method="post">
                @csrf

                @include('admin.donation-perk._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
