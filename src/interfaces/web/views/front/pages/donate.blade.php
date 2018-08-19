@extends('front.layouts.master')

@section('title', 'Donator List')
@section('description', "Annual donation statistics and a list of all players who have donated to Project City Build")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h2>Donations are the only way to keep our community running!</h2>

            <form action="{{ route('front.donate.charge') }}" method="POST">
            @csrf
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="{{ config('services.stripe.key') }}"
                data-amount="300"
                data-name="Project City Build"
                data-description="One-Time Donation"
                data-image="https://forums.projectcitybuild.com/uploads/default/original/1X/847344a324d7dc0d5d908e5cad5f53a61372aded.png"
                data-locale="auto"
                data-currency="aud">
            </script>
            </form>
        </div>

    </div>

@endsection