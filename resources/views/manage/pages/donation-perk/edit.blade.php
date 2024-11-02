@extends('manage.layouts.admin')

@section('title', 'Edit Donation Perk #' . $perk->donation_perks_id)

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.donation-perks.update', $perk) }}" method="post">
                @csrf
                @method('PUT')

                @include('manage.pages.donation-perk._form', ['buttonText' => 'Edit'])
            </form>
        </div>
    </div>
@endsection
